<?php
include_once( 'lib/global.php' );

// constant for session
$CURRENT_PAGE="INDEX";

if( !logged_in() )
{
    header( "Location: common_login/login.php" );
    exit(0);
}

print_header();
?>


<body lang="de" id="papst">

<!-- main container -->
<div class="w3-content w3-container" id="tk">

<!-- Content -->

<main>
  
  <h1>Useless Papst</h1>

  <h2>Litaneien</h2>
  
    <div id="actions" class="grid">
  
      <button class="party" id="fog"><span>Lass weißen Rauch aufsteigen!</span></button>
      <button class="party" id="led"><span>Beleuchtung ändern <span !id="aktuell"></span></span></button>
      <button class="party" id="inclight"><span>Mehr Licht!</span></button>
      <button class="party" id="declight"><span>Weniger Licht!</span></button>
      
    </div>
    
  <h2>Psalm erklingen lassen</h2>
    
    <div id="actions" class="grid">
      
      <button class="party" id="playhallelujah"><span>Sing <b>Hallelujah</b>!</span></button>
      <button class="party" id="playmotor"><span>Motorengeräusch!</span></button>
      <button class="party audio" id="sound_button_0"><span></span></button>
      <button class="party audio" id="sound_button_1"><span></span></button>
      <button class="party audio" id="sound_button_2"><span></span></button>
      <button class="party audio" id="sound_button_3"><span></span></button>
      <button class="party audio" id="sound_button_4"><span></span></button>
      <button class="party audio" id="sound_button_5"><span></span></button>
      <button class="party audio" id="sound_button_6"><span></span></button>
      <button class="party audio" id="sound_button_7"><span></span></button>
      <button class="party audio" id="sound_button_8"><span></span></button>
      <button class="party audio" id="sound_button_9"><span></span></button>
      <button class="party audio" id="sound_button_10"><span></span></button>
      <button class="party audio" id="sound_button_11"><span></span></button>
      <button class="party audio" id="sound_button_12"><span></span></button>
      <button class="party audio" id="sound_button_13"><span></span></button>
      <button class="party audio" id="sound_button_14"><span></span></button>
      <button class="party audio" id="sound_button_15"><span></span></button>
      <button class="party audio" id="sound_button_16"><span></span></button>
      <button class="party audio" id="sound_button_17"><span></span></button>
      <button class="party audio" id="sound_button_18"><span></span></button>
      <button class="party audio" id="sound_button_19"><span></span></button>
      <button class="party audio" id="sound_button_20"><span></span></button>
      <button class="party audio" id="sound_button_21"><span></span></button>
      <button class="party audio" id="sound_button_22"><span></span></button>
      <button class="party audio" id="sound_button_23"><span></span></button>
      <button class="party audio" id="sound_button_24"><span></span></button>
      <button class="party audio" id="sound_button_25"><span></span></button>
      <button class="party audio" id="sound_button_26"><span></span></button>
      <button class="party audio" id="sound_button_27"><span></span></button>
      <button class="party audio" id="sound_button_28"><span></span></button>
      <button class="party audio" id="sound_button_29"><span></span></button>      
      <button class="party audio" id="sound_button_30"><span></span></button>
      <button class="party audio" id="sound_button_31"><span></span></button>
      <button class="party audio" id="sound_button_32"><span></span></button>
      <button class="party audio" id="sound_button_33"><span></span></button>
      <button class="party audio" id="sound_button_34"><span></span></button>
      <button class="party audio" id="sound_button_35"><span></span></button>
      <button class="party audio" id="sound_button_36"><span></span></button>
      <button class="party audio" id="sound_button_37"><span></span></button>
      <button class="party audio" id="sound_button_38"><span></span></button>
      <button class="party audio" id="sound_button_39"><span></span></button>
      <button class="party audio" id="sound_button_40"><span></span></button>
      <button class="party audio" id="sound_button_41"><span></span></button>
      <button class="party audio" id="sound_button_42"><span></span></button>
      <button class="party audio" id="sound_button_43"><span></span></button>
      <button class="party audio" id="sound_button_44"><span></span></button>
      <button class="party audio" id="sound_button_45"><span></span></button>
      <button class="party audio" id="sound_button_46"><span></span></button>
      <button class="party audio" id="sound_button_47"><span></span></button>
      <button class="party audio" id="sound_button_48"><span></span></button>
      <button class="party audio" id="sound_button_49"><span></span></button>
      <button class="party audio" id="sound_button_50"><span></span></button>
      <button class="party audio" id="sound_button_51"><span></span></button>
      <button class="party audio" id="sound_button_52"><span></span></button>
      <button class="party audio" id="sound_button_53"><span></span></button>
      <button class="party audio" id="sound_button_54"><span></span></button>
      <button class="party audio" id="sound_button_55"><span></span></button>
      <button class="party audio" id="sound_button_56"><span></span></button>
      <button class="party audio" id="sound_button_57"><span></span></button>
      <button class="party audio" id="sound_button_58"><span></span></button>
      <button class="party audio" id="sound_button_59"><span></span></button>      
      <button class="party audio" id="sound_button_60"><span></span></button>
      <button class="party audio" id="sound_button_61"><span></span></button>
      <button class="party audio" id="sound_button_62"><span></span></button>
      <button class="party audio" id="sound_button_63"><span></span></button>
      <button class="party audio" id="sound_button_64"><span></span></button>
      <button class="party audio" id="sound_button_65"><span></span></button>
      <button class="party audio" id="sound_button_66"><span></span></button>
      <button class="party audio" id="sound_button_67"><span></span></button>
      <button class="party audio" id="sound_button_68"><span></span></button>
      <button class="party audio" id="sound_button_69"><span></span></button>
      <button class="party audio" id="sound_button_70"><span></span></button>
      <button class="party audio" id="sound_button_71"><span></span></button>
      <button class="party audio" id="sound_button_72"><span></span></button>
      <button class="party audio" id="sound_button_73"><span></span></button>
      <button class="party audio" id="sound_button_74"><span></span></button>
      <button class="party audio" id="sound_button_75"><span></span></button>
      <button class="party audio" id="sound_button_76"><span></span></button>
      <button class="party audio" id="sound_button_77"><span></span></button>
      <button class="party audio" id="sound_button_78"><span></span></button>
      <button class="party audio" id="sound_button_79"><span></span></button>
      <button class="party audio" id="sound_button_80"><span></span></button>
      <button class="party audio" id="sound_button_81"><span></span></button>
      <button class="party audio" id="sound_button_82"><span></span></button>
      <button class="party audio" id="sound_button_83"><span></span></button>
      <button class="party audio" id="sound_button_84"><span></span></button>
      <button class="party audio" id="sound_button_85"><span></span></button>
      <button class="party audio" id="sound_button_86"><span></span></button>
      <button class="party audio" id="sound_button_87"><span></span></button>
      <button class="party audio" id="sound_button_88"><span></span></button>
      <button class="party audio" id="sound_button_89"><span></span></button>      
      <button class="party audio" id="sound_button_90"><span></span></button>
      <button class="party audio" id="sound_button_91"><span></span></button>
      <button class="party audio" id="sound_button_92"><span></span></button>
      <button class="party audio" id="sound_button_93"><span></span></button>
      <button class="party audio" id="sound_button_94"><span></span></button>
      <button class="party audio" id="sound_button_95"><span></span></button>
      <button class="party audio" id="sound_button_96"><span></span></button>
      <button class="party audio" id="sound_button_97"><span></span></button>
      <button class="party audio" id="sound_button_98"><span></span></button>
      <button class="party audio" id="sound_button_99"><span></span></button>
  
    </div>
  
  <h2>Apostelberichte</h2>
  
    <div id="stats">
    
     <div class="row">
        <div class="column">Papst ist online seit:</div>
        <div class="column"><span class="highlight"><span id="boottime"></span></span></div>
     </div>
     <div class="row">
        <div class="column">Zeit seit der Schöpfung:</div>
        <div class="column"><div id="uptime"><span class="hidden" id="uptimevalue">0</span><span class="highlight"></span></div></div>
     </div>
     <div class="row">
        <div class="column">Umdrehungen:</div>
        <div class="column"><span class="highlight"><span class="blink_me" id="umdrehungen">1376</span></span></div>
     </div>
     <div class="row">
        <div class="column">Himmlische Frequenz:</div>
        <div class="column"><span class="highlight"><span class="blink_me" id="frequenz">80</span> Umdrehungen pro Minute</span></div>
     </div>
     <div class="row">
        <div class="column">Leuchtende Lichter:</div>
        <div class="column"><span class="highlight"><span id="active-leds"></span>/1024</span><span class="hidden" id="active-leds-data">0</span></div>
     </div>
     <div class="row">
        <div class="column">Illumination:</div>
        <div class="column"><span class="highlight" id="helligkeit">20%</span></div>
     </div>
     <div class="row">
        <div class="column">Sancta Combustia:</div>
        <div class="column"><span class="highlight"><span id="cputemp"></span>° (Temperatur des Höllenfeuers)</span></div>
     </div>
     <div class="row">
        <div class="column">Eifrigste Gläubige:</div>
        <div class="column"><span class="highlight" id="user1">der.mucki (97 actions)</span><br><span class="highlight" id="user2">L@neTheP@in (66 actions)</span><br><span class="highlight" id="user3">FatBeard (23 actions)</span></div>
     </div>
     <div class="row">
        <div class="column">Psalm der Woche:</div>
        <div class="column"><span class="highlight" id="mostplayedsound">"UT3_Multikill.wav"</span></div>
     </div>
     <div class="row">
        <div class="column">Zählung der pontifikalen Taten:</div>
        <div class="column"><span class="highlight" id="totalactions">129</span></div>
     </div>
     
    </div>
    
  <h2>Protokoll der Predigt</h2>
    
	<iframe src="log.php" title="event log" id="eventlog" height="300px"></iframe>
  
  <h2>Sakristei</h2>
  
    <div id="admin">
      <a href="common_login/logout.php"><button><span>Exkommunikation einleiten</span></button></a>
      <a href="common_login/admin_myself.php"><button><span>Geheimes Zugriffswort erneuern</span></button></a>
    </div>
  
</main>

<?php
print_footer();
?>

</div> <!-- main container #tk -->






<script>

// party
$(".party").click(function() {
    party.sparkles(this, {
        count: party.variation.range(15, 25),
	speed: party.variation.range(240, 270),
	size: party.variation.range(0.5, 1.2),
	lifetime: party.variation.range(0.3, 1.45),
    });
});



// open modal + add browser history-forward state
function openmodal(modalId) {
  $(modalId).addClass('modal-visible');
  window.history.pushState({ isPopup: true }, "modal");
}

// close any open modal + stop any audio + rewind it to the beginning
function hidemodal() {
  $('.modal').removeClass('modal-visible');
  $('audio').each(function() {
    this.pause();
    this.currentTime = 0;
  });
}

// to close a modal, call this function
// it actually sends a browser history-back state (see last function)
// via https://stackoverflow.com/questions/69706014
function closemodal() {
  window.history.back();
}

// script to close a modal when clicked outside of it
// is also sends a browser history-back state (see above)
window.onclick = function(event) {
  if (event.target.classList.contains("modal")) {
    window.history.back();
  }
}

// on browser history-back, call funtion "hidemodal" that closes any open modal
window.addEventListener('popstate', event => {
  if (event.state?.isPopup) {
    hidemodal();
  }
});



// jitter on active LEDs

(() => {
  const el = document.getElementById("active-leds");
  if (!el) return;

  // --- Tunables ---
  const RANGE = 12;          // ± fluctuation around the actual value
  const INTERVAL_MS = 100;   // how often to update the visual number
  const STEP = 5;            // max random step size per tick (smaller = smoother)
  const SMOOTH_MS = 3000;    // crawl duration from old to new actual

  // --- State ---
  const clamp = (v, min, max) => Math.min(max, Math.max(min, v));
  let actual = clamp(parseInt(el.textContent, 10) || 0, 0, 1024);
  let offset = 0; // relative deviation from the displayed baseline, kept in [-RANGE, RANGE]

  // displayed baseline that crawls towards `actual`
  let actualDisplay = actual;
  let tweenStart = 0;
  let tweenEnd = 0;
  let tweenFrom = actual;
  let tweenTo = actual;
  let lastDisplayed = null;

  // Gentle easing
  const easeInOut = (t) => (t < 0.5 ? 2*t*t : 1 - Math.pow(-2*t + 2, 2) / 2);

  // Render once
  el.textContent = String(actual);

  setInterval(() => {
    const now = Date.now();

    // If a tween is active, ease actualDisplay toward `actual`
    if (now < tweenEnd) {
      const t = clamp((now - tweenStart) / SMOOTH_MS, 0, 1);
      const eased = easeInOut(t);
      actualDisplay = clamp(tweenFrom + (tweenTo - tweenFrom) * eased, 0, 1024);
    } else {
      actualDisplay = tweenTo; // land exactly on target
    }

    // Jitter: bounded random walk around the crawling baseline
    const delta = Math.floor(Math.random() * (2 * STEP + 1)) - STEP; // [-STEP, STEP]
    offset = clamp(offset + delta, -RANGE, RANGE);

    // compute candidate display
    let displayed = clamp(Math.round(actualDisplay + offset), 0, 1024);

    // avoid repeating the same integer twice in a row
    if (displayed === lastDisplayed) {
      // try nudging the offset by +1 or -1 (within [-RANGE, RANGE])
      // pick a direction that moves away from lastDisplayed
      const tryNudge = (nudge) => {
        const newOffset = clamp(offset + nudge, -RANGE, RANGE);
        return clamp(Math.round(actualDisplay + newOffset), 0, 1024);
      };

      const preferUp = (actualDisplay + offset) <= lastDisplayed;
      let alt = tryNudge(preferUp ? +1 : -1);

      if (alt === lastDisplayed) {
        // fallback: try the other direction
        alt = tryNudge(preferUp ? -1 : +1);
      }

      // Only accept the alternative if it differs
      if (alt !== lastDisplayed) {
        // update offset to match the accepted alt (recompute once)
        offset = clamp(offset + (alt > lastDisplayed ? (preferUp ? +1 : -1) : (preferUp ? -1 : +1)), -RANGE, RANGE);
        displayed = alt;
      }
      // If both attempts still equal lastDisplayed (rare rounding case), we keep 'displayed' as is.
    }

    // always show 4 digits with leading zeros
    el.textContent = displayed.toString().padStart(4, "0");
    lastDisplayed = displayed;
  }, INTERVAL_MS);

  // Call this when the server sends a new value
  window.updateActiveLeds = (value) => {
    actual = clamp(Number(value) || 0, 0, 1024);
    tweenFrom = actualDisplay;
    tweenTo = actual;
    tweenStart = Date.now();
    tweenEnd = tweenStart + SMOOTH_MS;
  };

  // ---- demo server updates every 10s (remove in production) ----
  //setInterval(() => window.updateActiveLeds(Math.floor(Math.random() * 1025)), 10000);
  setInterval(() => {
    var current_active_leds = $( "#active-leds-data" ).text();
    window.updateActiveLeds(current_active_leds);
  }, 10000);
})();



// uptime calculator (from seconds)

(() => {
  const container = document.getElementById("uptime");
  if (!container) return;

  const hidden = container.querySelector(".hidden");
  const highlight = container.querySelector(".highlight");

  // Converts seconds → readable string
  const formatUptime = (seconds) => {
    let totalMinutes = Math.floor(seconds / 60);

    const days = Math.floor(totalMinutes / (60 * 24));
    totalMinutes -= days * 60 * 24;

    const hours = Math.floor(totalMinutes / 60);
    totalMinutes -= hours * 60;

    const minutes = totalMinutes;

    const parts = [];
    if (days > 0) parts.push(`${days} Tag${days !== 1 ? "e" : ""}`);
    if (hours > 0) parts.push(`${hours} Stunde${hours !== 1 ? "n" : ""}`);
    parts.push(`${minutes} Minute${minutes !== 1 ? "n" : ""}`);

    return parts.join(", ");
  };

  // Updates display based on current hidden value
  const updateDisplay = () => {
    const seconds = Number(hidden.textContent.trim());
    if (Number.isFinite(seconds)) {
      highlight.textContent = formatUptime(seconds);
    } else {
      highlight.textContent = "";
    }
  };

  // Run once initially
  updateDisplay();

  // Automatically update when the hidden span changes
  const observer = new MutationObserver(updateDisplay);
  observer.observe(hidden, { childList: true, characterData: true, subtree: true });

})();



// reload iFrame

  setInterval(() => {
    const f = document.getElementById("eventlog");
    if (f) f.src = f.src;     // reassigning the same src forces a reload
  }, 60000); // 60 seconds



</script>

</body>

</html>
