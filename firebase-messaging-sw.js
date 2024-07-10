importScripts("https://www.gstatic.com/firebasejs/10.12.3/firebase-app.js");
importScripts('https://www.gstatic.com/firebasejs/10.12.3/firebase-analytics.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.3/firebase-messaging.js');

const firebaseConfig = {
    apiKey: "AIzaSyAdwWzTaOy67DZ1qimvhfxtW_zfRzIxvP4",
    authDomain: "fdscard-778d9.firebaseapp.com",
    projectId: "fdscard-778d9",
    storageBucket: "fdscard-778d9.appspot.com",
    messagingSenderId: "733994430317",
    appId: "1:733994430317:web:1e7e06c87617fb85a27464",
    measurementId: "G-D3SJHW5CDT"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// Background message handler
messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
