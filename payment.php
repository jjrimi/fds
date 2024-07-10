<?php
session_start();
include "./db_conn.php";
date_default_timezone_set('Asia/Seoul');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data === null) {
        echo "JSON 디코딩 에러: " . json_last_error_msg();
        exit;
    }

    $card_number = $data['card_number'];
    $age = $data['age'];
    $gender = $data['gender'];
    $category = $data['category'];
    $amount = $data['amount'];

    $stmt = $conn->prepare("SELECT user_id, card_id FROM Cards WHERE card_number = ?");
    $stmt->bind_param("s", $card_number);
    $stmt->execute();
    $stmt->bind_result($user_id, $card_id);
    $stmt->fetch();
    $stmt->close();

    if (!$user_id || !$card_id) {
        echo "유효하지 않은 카드 번호입니다.";
        exit;
    }

    $current_time = date('Y-m-d H:i:s');

    // 결제 요청 저장
    $stmt = $conn->prepare("INSERT INTO Payment_Requests (card_id, user_id, request_date, amount, request_status) VALUES (?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("iisd", $card_id, $user_id, $current_time, $amount);
    $stmt->execute();
    $request_id = $stmt->insert_id;
    $stmt->close();

    // 인공지능 모델에 데이터 전송
    $model_data = array('age' => $age, 'gender' => $gender, 'category' => $category, 'amount' => $amount);
    $model_payload = json_encode($model_data);

    $ch = curl_init('http://flask:5000/predict');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $model_payload);

    $model_result = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        echo "cURL error: " . $error_msg;
    } else {
        // 응답 결과 확인
        echo "모델 응답: " . $model_result; // 응답 결과를 출력해 디버깅
    }

    curl_close($ch);

    $model_response = json_decode($model_result, true);

    if ($model_response === null) {
        echo "JSON 디코딩 에러: " . json_last_error_msg();
        echo "모델 응답 (디버그): " . $model_result;
        exit;
    }

    $prediction = $model_response['prediction'];

    // Payment_Requests 테이블 업데이트
    $stmt = $conn->prepare("UPDATE Payment_Requests SET request_status = ? WHERE request_id = ?");
    $request_status = $prediction ? 'Declined' : 'Approved';
    $fraud_flag = $prediction;
    $stmt->bind_param("si", $request_status, $request_id);
    $stmt->execute();
    $stmt->close();

    // Fraud_Detections 테이블에 결과 저장
    $stmt = $conn->prepare("INSERT INTO Fraud_Detections (request_id, detection_date, detection_result) VALUES (?, ?, ?)");
    $detection_result = $prediction ? 1 : 0;
    $stmt->bind_param("isi", $request_id, $current_time, $fraud_flag);
    $stmt->execute();
    $detection_id = $stmt->insert_id;
    $stmt->close();

    if ($detection_result == 1) {
        $stmt = $conn->prepare("SELECT family_id FROM Family WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($family_id);

        $family_user_ids = [];
        while ($stmt->fetch()) {
            $family_user_ids[] = $family_id;
        }
        $stmt->close();

        foreach ($family_user_ids as $family_id) {
            $stmt = $conn->prepare("INSERT INTO Notifications (detection_id, family_id, notification_date, notification_status) VALUES (?, ?, ?, 'Failed')");
            $stmt->bind_param("iis", $detection_id, $family_id, $current_time);
            $stmt->execute();
            $stmt->close();
        }
    }

    // 결과 전송
    echo json_encode(['result' => !$prediction]);
}
?>