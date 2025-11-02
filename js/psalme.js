$(function() {

  const sprueche = [
    { de: "Im Namen des Bits, des Bytes und des Heiligen Scripts.",
      la: "In nomine Bitis, Bytis et Spiritus Scripti." },
    { de: "Bevor du den Server siehst, musst du an ihn glauben.",
      la: "Antequam servatorem vides, credere debes." },
    { de: "Und der Papst sprach: sudo apt-get install lumen.",
      la: "Et dixit Pontifex: fiat lux per sudo." },
    { de: "Der Herr prüft dich mit jedem Reboot.",
      la: "Dominus te probat per reinitium." },
    { de: "Wer im Netz bleibt, wird selig.",
      la: "Qui in rete manet, salvabitur." },
    { de: "Denn dein ist der Code, die Schleife und das Kernelreich.",
      la: "Tuum est codex, et loop, et regnum kernel." },
    { de: "Gib deinem Pi heute seine tägliche Spannung.",
      la: "Da Pi nostro hodie voltam quotidianam." },
    { de: "Wo zwei oder drei im WLAN versammelt sind, da ist der Papst mitten unter ihnen.",
      la: "Ubi duo vel tres in WLAN congregati sunt, ibi Pontifex in medio eorum est." },
    { de: "Der Segen des WLAN sei mit dir und deinem Router.",
      la: "Benedictio retis tecum et cum router tuo sit." },
    { de: "Wer im Cache sündigt, soll im Log Buße tun.",
      la: "Qui in cache peccat, in registro paenitentiam agat." },
    { de: "Und siehe, der Buffer war voll, und großes Wehklagen erhob sich.",
      la: "Et ecce, buffer plenus erat, et factus est clamor magnus." },
    { de: "Glücklich, wer den Shutdown rechtzeitig erkennt.",
      la: "Felix est qui clausuram tempestive cognoscit." },
    { de: "Reboote täglich, damit dein Herz nicht einfriert.",
      la: "Reinicia cotidie, ne cor tuum congelescat." },
    { de: "Im Anfang war der Pi, und der Pi war mit dem Licht.",
      la: "In principio erat Pi, et Pi erat cum luce." },
    { de: "Gesegnet sei derjenige, der den Debug findet.",
      la: "Benedictus qui errorem deprehendit." },
    { de: "Der Papst gibt, der Papst nimmt.",
      la: "Pontifex dat, Pontifex tollit." },
    { de: "Wer sein Passwort vergisst, der verliere das Himmelreich.",
      la: "Qui verbum suum obliviscitur, regnum caelorum amittit." },
    { de: "Amen ist kein Befehl, sondern eine Bestätigung.",
      la: "Amen non mandatum est, sed confirmatio." }
  ];

  function neuerSpruch() {
    const s = sprueche[Math.floor(Math.random() * sprueche.length)];
    $("#deutsch").fadeOut(200, function() {
      $(this).text(s.de).fadeIn(400);
    });
    $("#latein").fadeOut(200, function() {
      $(this).text(s.la).fadeIn(400);
    });
  }

  neuerSpruch();
  setInterval(neuerSpruch, 100000);

});
