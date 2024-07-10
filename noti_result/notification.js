document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM fully loaded and parsed");

    const enableNotificationsCheckbox = document.getElementById("enable-notifications");
    const saveSettingsButton = document.getElementById("save-settings");

    loadSettings();

    saveSettingsButton.addEventListener("click", function() {
        saveSettings();
    });

    function loadSettings() {
        console.log("Loading settings...");
        const notificationsEnabled = localStorage.getItem("notificationsEnabled") === "true";
        enableNotificationsCheckbox.checked = notificationsEnabled;
        console.log("Notifications enabled:", notificationsEnabled);
    }

    function saveSettings() {
        console.log("Saving settings...");
        const notificationsEnabled = enableNotificationsCheckbox.checked;

        if (notificationsEnabled) {
            requestNotificationPermission().then(permission => {
                if (permission === "granted") {
                    localStorage.setItem("notificationsEnabled", "true");
                    alert("알림이 활성화되었습니다.");
                    console.log("Notifications enabled.");
                } else {
                    enableNotificationsCheckbox.checked = false;
                    localStorage.setItem("notificationsEnabled", "false");
                    alert("알림이 활성화되지 않았습니다. 브라우저 설정에서 알림을 허용해 주세요.");
                    console.log("Notifications not enabled.");
                }
            });
        } else {
            localStorage.setItem("notificationsEnabled", "false");
            alert("알림이 비활성화되었습니다.");
            console.log("Notifications disabled.");
        }
    }

    function requestNotificationPermission() {
        return new Promise((resolve, reject) => {
            if (!("Notification" in window)) {
                alert("이 브라우저는 데스크탑 알림을 지원하지 않습니다.");
                resolve("denied");
                console.log("Browser does not support notifications.");
            } else if (Notification.permission === "granted") {
                resolve("granted");
                console.log("Notification permission already granted.");
            } else if (Notification.permission !== "denied") {
                Notification.requestPermission().then(permission => {
                    resolve(permission);
                    console.log("Notification permission requested:", permission);
                });
            } else {
                resolve("denied");
                console.log("Notification permission denied.");
            }
        });
    }

    function showNotification(title, options) {
        console.log("Attempting to show notification...");
        if (localStorage.getItem("notificationsEnabled") === "true" && Notification.permission === "granted") {
            new Notification(title, options);
            console.log("Notification shown:", title, options);
        } else {
            console.log("Notifications not enabled or permission not granted.");
        }
    }

    function getSessionUserId() {
        return fetch('/noti_result/get_logged_in_user_id.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP 오류! 상태: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                return data.user_id;
            })
            .catch(error => {
                console.error('세션 정보 가져오는 중 오류 발생:', error);
                return null;
            });
    }

    async function checkAndSendNotifications() {
        console.log("Checking and sending notifications...");
        const sessionUserId = await getSessionUserId();
        if (!sessionUserId) {
            console.log("User not logged in.");
            return;
        }

        fetch('/noti_result/check_notifications.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP 오류! 상태: ${response.status}`);
                }
                console.log("Notifications fetched successfully.");
                return response.json();
            })
            .then(data => {
                console.log("Fetched notifications data:", data);
                data.forEach(notification => {
                    if (notification.notification_status === 'Failed' && notification.family_user_id == sessionUserId) {
                        showNotification("사기 탐지 경고", {
                            body: "사기 결제가 감지되었습니다. 결제 취소 또는 가족들에게 연락하세요.",
                            icon: "./icon.png"
                        });
                        updateNotificationStatus(notification.notification_id, 'Sent');
                    }
                });
            })
            .catch(error => console.error('알림을 가져오는 중 오류 발생:', error));
    }

    function updateNotificationStatus(notificationId, status) {
        console.log("Updating notification status...", notificationId, status);
        fetch('/noti_result/update_notification_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ notification_id: notificationId, status: status })
        })
        .then(response => {
            if (response.ok) {
                console.log("Notification status updated successfully.");
            } else {
                console.error("Failed to update notification status:", response.statusText);
            }
        })
        .catch(error => console.error('알림 상태 업데이트 중 오류 발생:', error));
    }

    setInterval(checkAndSendNotifications, 5000);
});

// 초기 알림 테스트
setTimeout(() => {
    showNotification("안녕하세요!", {
        body: "이것은 알림입니다.",
        icon: "./icon.png"
    });
}, 5000);