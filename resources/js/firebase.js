import firebase from 'firebase/app'
import "firebase/analytics";
import 'firebase/firebase-messaging'
import axios from "axios";

const firebaseConfig = {
    apiKey: "AIzaSyDlJjbLe9GcEOVSKpCnT3Xin1hF6Rj2AEc",
    authDomain: "xctp-copito.firebaseapp.com",
    projectId: "xctp-copito",
    storageBucket: "xctp-copito.appspot.com",
    messagingSenderId: "963411504247",
    appId: "1:963411504247:web:db4c03350fc1ca17c5ea72",
    measurementId: "G-2JWJTWW01W"
};

firebase.initializeApp(firebaseConfig)
const analytics = firebase.analytics();

const messaging =firebase.messaging();
    messaging.getToken({vapidKey: "BCh0lo5r7kS5T777wXDvyN87J2j_uVSWQZy092QuigHK3ZIyYKdGjo7s7YqhRksd8qSBA7Uya_ZVEKA1Bf02L_Q"})
    // messaging.getToken()
        .then((currentToken) => {
            if (currentToken) {
                let form = new FormData();
                form.append('token', currentToken)
                axios.post('/savePush',form)
                    .then(() => {
                    })
                    .catch(error => {
                        // handle error
                        this.errors = error
                        console.log(error);
                    })
            } else {
                console.log('No registration token available. Request permission to generate one.');
            }
        }).catch((err) => {
        console.log('An error occurred while retrieving token. ', err);
    });
export default messaging;
