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
  
  <h1>Useless Battlefield</h1>

  <h2>Aktuelles Game</h2>
  
   <div id="stats">
    
     <div class="row">
        <div class="column">Map:</div>
        <div class="column"><span class="highlight bf_map" id="bf_map">–</span></div>
     </div>
     <div class="row">
        <div class="column">Server:</div>
        <div class="column"><span class="highlight bf_hoster" id="bf_hoster">–</span></div>
     </div>
     <div class="row">
        <div class="column">Spieler:</div>
        <div class="column"><span class="highlight" id="bf_numplayers">–</span></div>
     </div>
     <div class="row">
        <div class="column">Tickets:</div>
        <div class="column"><span class="highlight" id="bf_tickets">–</span></div>
     </div>
     <div class="row">
        <div class="column">Rundenzeit:</div>
        <div class="column"><span class="highlight" id="bf_round_time">–</span></div>
     </div>
     <div class="row">
        <div class="column">Status:</div>
        <div class="column"><span class="highlight" id="bf_online">–</span></div>
     </div>
   
   </div>
  
  
  <h2>Statistik</h2>
  
    <div id="bf-stats">
  
    <table>

    <thead>
    <tr>
        <th></th>
        <th>Spieler</th>
        <th>Punkte</th>
        <th title="Siege als bester Spieler des Siegerteams (aus dem Rundenarchiv)">Man of the Match</th>
        <th>Kills</th>
        <th>Deaths</th>
    </tr>
    </thead>

    <tbody id="bf-stats-body">
    <tr>
        <td colspan="6">Lade Statistik…</td>
    </tr>
    </tbody>
    
    </table>
      
    </div>
  
    <button id="bf-suggest" type="button"><span>Team vorschlagen</span></button>

    <div id="bf-teams" class="grid">
    
        <div>
            <p>Alliierte</p>
            <p>Lade Teams…</p>
        </div>

        <div>
            <p>Achse</p>
            <p>Lade Teams…</p>
        </div>
    
    </div>

  <h2>Sakristei</h2>
  
    <div id="admin">
      <a href="index.php"><button><span>Zurück zum Useless Papst</span></button></a>
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
