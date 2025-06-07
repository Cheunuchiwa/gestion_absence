// This file contains the JavaScript for the verification code functionality

document.addEventListener("DOMContentLoaded", () => {
  // Get the submit button
  const submitButton = document.querySelector(".btn-submit")
  const originalForm = document.querySelector(".attendance-form form")

  // Create verification modal elements
  const modal = document.createElement("div")
  modal.className = "verification-modal"
  modal.style.display = "none"

  modal.innerHTML = `
    <div class="verification-content">
      <h3><i class="fas fa-lock"></i> Vérification</h3>
      <p>Un code de vérification a été envoyé à djoumessiivan2006@gmail.com</p>
      <div class="code-input-container">
        <input type="text" id="verification-code" placeholder="Entrez le code à 4 chiffres" maxlength="4">
      </div>
      <div class="verification-buttons">
        <button type="button" id="verify-code" class="btn-verify">Vérifier</button>
        <button type="button" id="cancel-verification" class="btn-cancel">Annuler</button>
      </div>
    </div>
  `

  document.body.appendChild(modal)

  // Store the generated code
  let generatedCode = ""

  // Override the default form submission
  if (originalForm) {
    originalForm.addEventListener("submit", (e) => {
      e.preventDefault()

      // Generate a random 4-digit code
      generatedCode = Math.floor(1000 + Math.random() * 9000).toString()

      // Call the PHP sendMail function using fetch API
      fetch("send-verification.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `code=${generatedCode}`,
      })
        .then((response) => response.text())
        .then((data) => {
          console.log("Email sent:", data)
          // Show the verification modal
          modal.style.display = "flex"
        })
        .catch((error) => {
          console.error("Error sending email:", error)
          showNotification("Erreur lors de l'envoi du code de vérification", "error")
        })
    })
  }

  // Verify code button click handler
  document.getElementById("verify-code").addEventListener("click", () => {
    const enteredCode = document.getElementById("verification-code").value

    if (enteredCode === generatedCode) {
      // Code is correct, submit the form
      modal.style.display = "none"
      showNotification("Code vérifié avec succès! Soumission de l'appel...", "success")

      // Actually submit the form
      originalForm.removeEventListener("submit", () => {})
      setTimeout(() => {
        originalForm.submit()
      }, 1000)
    } else {
      // Code is incorrect
      showNotification("Code incorrect. Veuillez réessayer.", "error")
    }
  })

  // Cancel button click handler
  document.getElementById("cancel-verification").addEventListener("click", () => {
    modal.style.display = "none"
  })

  // Function to show notifications
  function showNotification(message, type) {
    const notification = document.getElementById("notification")
    const notificationMessage = document.getElementById("notification-message")

    notificationMessage.textContent = message
    notification.className = `notification ${type}`
    notification.style.display = "block"

    setTimeout(() => {
      notification.style.display = "none"
    }, 3000)
  }
})
