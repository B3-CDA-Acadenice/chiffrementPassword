import CryptoJS from "crypto-js"; // Si framework JS utilisé 

const secretKey = "MaCléSecrèteUltraSécure"; // Clé de sécurité à changer

function encryptPassword(password) {
    return CryptoJS.AES.encrypt(password, secretKey).toString();
}

async function loginUser(email, password) {
    const encryptedPassword = encryptPassword(password); // Chiffrement

    const response = await fetch("http://127.0.0.1:8000/api/login", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ email, password: encryptedPassword }),
    });

    const data = await response.json();
    console.log(data);
}

let email = "lynna@testo.fr";
let password = "password";
console.log(loginUser(email, password)); // Appel de la fonction