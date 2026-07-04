/**
 * Effets sonores rétro générés en Web Audio API (aucun fichier audio requis).
 * Préférence muet/son mémorisée dans localStorage.
 */
window.SoundManager = (() => {
  let ctx = null;
  let muted = localStorage.getItem("memory_muted") === "1";

  function getContext() {
    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
    if (!AudioContextClass) return null;
    if (!ctx) ctx = new AudioContextClass();
    if (ctx.state === "suspended") ctx.resume();
    return ctx;
  }

  function tone(freq, duration, { type = "square", delay = 0, gain = 0.08 } = {}) {
    if (muted) return;
    const audio = getContext();
    if (!audio) return;

    const osc = audio.createOscillator();
    const gainNode = audio.createGain();
    osc.type = type;
    osc.frequency.value = freq;

    const startTime = audio.currentTime + delay;
    gainNode.gain.setValueAtTime(gain, startTime);
    gainNode.gain.exponentialRampToValueAtTime(0.0001, startTime + duration);

    osc.connect(gainNode);
    gainNode.connect(audio.destination);
    osc.start(startTime);
    osc.stop(startTime + duration);
  }

  return {
    flip() {
      tone(520, 0.08);
    },
    match() {
      tone(660, 0.1);
      tone(880, 0.12, { delay: 0.09 });
    },
    error() {
      tone(160, 0.18, { type: "sawtooth", gain: 0.06 });
    },
    win() {
      [523, 659, 784, 1047].forEach((f, i) => tone(f, 0.15, { delay: i * 0.12 }));
    },
    timeout() {
      [392, 330, 262].forEach((f, i) =>
        tone(f, 0.2, { type: "sawtooth", delay: i * 0.15, gain: 0.06 })
      );
    },
    toggleMute() {
      muted = !muted;
      localStorage.setItem("memory_muted", muted ? "1" : "0");
      return muted;
    },
    isMuted() {
      return muted;
    },
  };
})();
