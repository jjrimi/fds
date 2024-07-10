<?php
// sendNotification.php
require 'vendor/autoload.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

include("../db_conn.php");
$username = 'user';
$password = 'user1234';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 사용자 ID를 기반으로 구독 정보를 가져옵니다.
    $user_id = 1; // 실제 사용자 ID를 세션 또는 기타 인증 방법을 통해 얻어야 합니다.
    $stmt = $pdo->prepare("SELECT endpoint, p256dh, auth FROM Subscriptions WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($subscriptions)) {
        echo "No subscriptions found for user ID $user_id.";
        exit;
    }

    $auth = [
        'VAPID' => [
            'subject' => 'taccent2@gmail.com',
            'publicKey' => 'BBkL8jZ_j7kUimaKUZh9E8sB68rDje81cli_tVR_5G4FpDaHgEKvg_i8kiSyGJghvqgdGm0LKe1yFwd7_-himag',  // 여기서 YOUR_PUBLIC_KEY는 생성한 VAPID 키의 공개 키입니다.
            'privateKey' => 'r3yUNsMYG4iglHrZ4EjvG5MBA_bsHUPR7QG-Rv03quA' // 여기서 YOUR_PRIVATE_KEY는 생성한 VAPID 키의 비공개 키입니다.
        ],
    ];

    $webPush = new WebPush($auth);

    foreach ($subscriptions as $sub) {
        $subscription = Subscription::create([
            'endpoint' => $sub['endpoint'],
            'publicKey' => $sub['p256dh'],
            'authToken' => $sub['auth']
        ]);

        $report = $webPush->sendOneNotification(
            $subscription,
            json_encode(['title' => 'Test Notification', 'body' => 'This is a test push notification'])
        );

        if ($report->isSuccess()) {
            echo "Notification sent successfully!";
        } else {
            echo "Notification failed: " . $report->getReason();
        }
    }
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
