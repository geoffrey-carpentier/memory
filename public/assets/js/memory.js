/**
 * Script minimal pour gérer le chrono, les flips et l’envoi des stats.
 */
document.addEventListener("DOMContentLoaded", () => {
  const grid = document.querySelector(".board-grid");
  if (!grid) return; // Pas sur une page de jeu

  // Validation des données du DOM
  const totalPairs = parseInt(grid.dataset.totalPairs, 10);
  const initialTime = parseInt(grid.dataset.time, 10);

  if (isNaN(totalPairs) || isNaN(initialTime)) {
    console.error("Données de grille invalides.");
    return;
  }

  // Sélections DOM
  const statPairs = document.getElementById("stat-pairs");
  const statErrors = document.getElementById("stat-errors");
  const statTimer = document.getElementById("stat-timer");
  const finishForm = document.getElementById("finish-form");
  const pauseBtn = document.getElementById("pause-btn");
  const toast = document.getElementById("board-toast");

  if (!finishForm) {
    console.error("Formulaire de fin introuvable.");
    return;
  }

  const messages = {
    success: [
      "Bien joué !",
      "Encore un coup de chance !",
      "Plus que quelques paires !",
      "Continue comme ça !",
    ],
    fail: [
      "Raté...",
      "Essaye encore.",
      "Pas cette fois.",
      "Tu peux faire mieux !",
    ],
  };

  let timeRemaining = initialTime;
  let pairsFound = 0;
  let errors = 0;
  let lockBoard = false;
  let firstCard = null;
  let isPaused = false;

  const timerId = setInterval(() => {
    if (isPaused) return;

    timeRemaining = Math.max(0, timeRemaining - 1);
    if (statTimer) {
      statTimer.textContent = `Temps restant : ${timeRemaining} s`;
    }

    if (timeRemaining === 0) {
      finishForm.querySelector('input[name="result_type"]').value = "timeout";
      showToast("Tu es lent, beaucoup trop lent...", "fail");
      endGame();
    }
  }, 1000);

  // Gestion pause automatique au changement d'onglet
  document.addEventListener("visibilitychange", () => {
    if (document.hidden && !isPaused) {
      togglePause(true);
    }
  });

  // Raccourci clavier : barre d'espace pour pause
  document.addEventListener("keydown", (event) => {
    if (event.code === "Space" && grid.offsetParent !== null) {
      event.preventDefault();
      togglePause();
    }
  });

  // Bouton pause
  pauseBtn?.addEventListener("click", togglePause);

  function togglePause(forceState = null) {
    isPaused = forceState !== null ? forceState : !isPaused;

    if (pauseBtn) {
      pauseBtn.textContent = isPaused ? "▶ Reprendre" : "⏸ Pause";
      pauseBtn.setAttribute("aria-pressed", isPaused);
    }

    grid.classList.toggle("is-paused", isPaused);
  }

  // Écouteurs sur les cartes
  grid.querySelectorAll(".card").forEach((card) => {
    card.addEventListener("click", () => {
      if (isPaused || grid.classList.contains("is-paused")) return;
      handleFlip(card);
    });
  });

  function handleFlip(card) {
    if (
      lockBoard ||
      card.classList.contains("is-flipped") ||
      card.classList.contains("is-matched")
    ) {
      return;
    }

    card.classList.add("is-flipped");

    if (!firstCard) {
      firstCard = card;
      return;
    }

    const isMatch = firstCard.dataset.pair === card.dataset.pair;

    if (isMatch) {
      firstCard.classList.add("is-matched");
      card.classList.add("is-matched");
      pairsFound++;

      if (statPairs) {
        statPairs.textContent = `Paires trouvées : ${pairsFound} / ${totalPairs}`;
      }

      showToast(randomFrom(messages.success), "success");
      firstCard = null;

      if (pairsFound === totalPairs) {
        finishForm.querySelector('input[name="result_type"]').value = "win";
        setTimeout(endGame, 500);
      }
    } else {
      errors++;
      if (statErrors) {
        statErrors.textContent = `Erreurs : ${errors}`;
      }

      showToast(randomFrom(messages.fail), "fail");
      lockBoard = true;

      setTimeout(() => {
        firstCard.classList.remove("is-flipped");
        card.classList.remove("is-flipped");
        firstCard = null;
        lockBoard = false;
      }, 900);
    }
  }

  function randomFrom(list) {
    return list[Math.floor(Math.random() * list.length)];
  }

  function showToast(message, type) {
    if (!toast) return;

    toast.textContent = message;
    toast.className = `board-toast board-toast--${type} is-visible`;
    toast.setAttribute("role", "status");

    setTimeout(() => {
      toast.classList.remove("is-visible");
    }, 1000);
  }

  function endGame() {
    clearInterval(timerId);
    finishForm.querySelector('input[name="pairs_found"]').value = pairsFound;
    finishForm.querySelector('input[name="errors"]').value = errors;
    finishForm.querySelector('input[name="time_remaining"]').value =
      timeRemaining;
    finishForm.submit();
  }
});

("Continue !");
