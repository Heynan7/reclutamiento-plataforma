import { initializeApp } from "firebase/app";
import { getAuth, GoogleAuthProvider, signInWithPopup } from "firebase/auth";

// Config de tu proyecto Firebase
const firebaseConfig = {
  apiKey: "AIzaSyBoLxIKMInGZbRBZLqr7-Jy2D9nQ_Jm2pg",
  authDomain: "plataformaia-c591c.firebaseapp.com",
  projectId: "plataformaia-c591c",
  storageBucket: "plataformaia-c591c.firebasestorage.app",
  messagingSenderId: "20717697567",
  appId: "1:20717697567:web:496b26753398c41ef5f32a",
};

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const provider = new GoogleAuthProvider();

export async function getGoogleIdToken() {
  const result = await signInWithPopup(auth, provider);
  return await result.user.getIdToken();
}
