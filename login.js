import CryptoJS from "crypto-js"; // Assure-toi que CryptoJS est installé

const secretKey = "MaCléSecrèteUltraSécure"; // ⚠️ DOIT ÊTRE IDENTIQUE SUR LE BACK-END

function encryptPassword(password) {
    return CryptoJS.AES.encrypt(password, secretKey).toString();
}

async function loginUser(email, password, isEncrypted = false) {
    let encryptedPassword = isEncrypted ? password : encryptPassword(password); // Vérifie si déjà chiffré

    console.log("🔐 Mot de passe envoyé :", encryptedPassword); // Vérifie avant envoi

    const response = await fetch("http://127.0.0.1:8000/api/login", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ email, password: encryptedPassword }),
    });

    const data = await response.json();
    console.log("Réponse du serveur:", data);
}

// 🛠 Test
let email = "johnnydoe@example.com";
let password = "U2FsdGVkX1+J4xz9m6F3HfF5I"; // vrai mot de passe en clair
loginUser(email, password, false); // false = le mot de passe n'est pas encore chiffré
