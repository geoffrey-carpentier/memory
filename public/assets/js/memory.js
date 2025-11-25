/**
 * Script minimal pour gérer le chrono, les flips et l’envoi des stats.
 */
document.addEventListener("DOMContentLoaded", () => {
  const grid = document.querySelector(".board-grid");
  if (!grid) return;

  const totalPairs = parseInt(grid.dataset.totalPairs, 10);
  let timeRemaining = parseInt(grid.dataset.time, 10);
  let pairsFound = 0;
  let errors = 0;
  let lockBoard = false;
  let firstCard = null;

  const statPairs = document.getElementById("stat-pairs");
  const statErrors = document.getElementById("stat-errors");
  const statTimer = document.getElementById("stat-timer");
  const finishForm = document.getElementById("finish-form");

  const timerId = setInterval(() => {
    timeRemaining = Math.max(0, timeRemaining - 1);
    statTimer.textContent = `Temps restant : ${timeRemaining} s`;

    if (timeRemaining === 0) endGame();
  }, 1000);

  grid.querySelectorAll(".card").forEach((card) => {
    card.addEventListener("click", () => handleFlip(card));
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
      statPairs.textContent = `Paires trouvées : ${pairsFound} / ${totalPairs}`;
      firstCard = null;

      if (pairsFound === totalPairs) endGame();
    } else {
      errors++;
      statErrors.textContent = `Erreurs : ${errors}`;
      lockBoard = true;

      setTimeout(() => {
        firstCard.classList.remove("is-flipped");
        card.classList.remove("is-flipped");
        firstCard = null;
        lockBoard = false;
      }, 800);
    }
  }

  function endGame() {
    clearInterval(timerId);
    updateFormAndSubmit();
  }

  function updateFormAndSubmit() {
    finishForm.querySelector('input[name="pairs_found"]').value = pairsFound;
    finishForm.querySelector('input[name="errors"]').value = errors;
    finishForm.querySelector('input[name="time_remaining"]').value =
      timeRemaining;
    finishForm.submit();
  }
});
