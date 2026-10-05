<?php
// Set the time zone so the date matches New Jersey time
date_default_timezone_set("America/New_York");
$author = "Gustavo Garcia-Vargas";
$today  = date("l, F j, Y");

$num1 = "";
$num2 = "";
$output = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $num1 = trim($_POST["num1"] ?? "");
    $num2 = trim($_POST["num2"] ?? "");
    // Quote each argument separately so input can't break the command
    $command = "python3 sum.py " . escapeshellarg($num1) . " " . escapeshellarg($num2);
    // Run the Python script and capture what it prints
    $output = trim(shell_exec($command) ?? "");
}

// Helper to safely print user-supplied text into HTML
function h($s) { return htmlspecialchars($s, ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sum App | <?= h($author) ?></title>
<style>
  :root {
    --desk: #2E4A3F;
    --machine: #DCE3DA;
    --edge: #9FB0A4;
    --ink: #1E2B26;
    --paper: #FBFCF7;
    --red: #B3362A;
  }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    min-height: 100vh;
    background: var(--desk);
    color: var(--ink);
    font-family: "Segoe UI", system-ui, -apple-system, sans-serif;
    display: grid;
    place-items: center;
    padding: 2rem 1rem;
  }
  .machine {
    width: 100%;
    max-width: 420px;
    background: var(--machine);
    border: 2px solid var(--edge);
    border-radius: 18px 18px 8px 8px;
    padding: 1.75rem 1.5rem 1.5rem;
    box-shadow: 0 14px 0 -6px #1C2E27;
  }
  header h1 {
    margin: 0;
    font-family: Rockwell, "Roboto Slab", Georgia, serif;
    font-size: 1.9rem;
    line-height: 1.1;
  }
  header p { margin: .35rem 0 0; font-size: .95rem; }
  header .date { color: #4C5F56; }
  form { margin-top: 1.5rem; display: grid; gap: .9rem; }
  label { font-weight: 600; font-size: .9rem; display: block; margin-bottom: .3rem; }
  input[type="text"] {
    width: 100%;
    padding: .7rem .8rem;
    font: 1.15rem ui-monospace, Consolas, monospace;
    text-align: right;
    border: 2px solid var(--edge);
    border-radius: 6px;
    background: var(--paper);
    color: var(--ink);
  }
  input[type="text"]:focus, button:focus-visible {
    outline: 3px solid var(--red);
    outline-offset: 2px;
  }
  button {
    margin-top: .4rem;
    padding: .8rem;
    font-size: 1.05rem;
    font-weight: 700;
    color: #fff;
    background: var(--red);
    border: none;
    border-radius: 6px;
    box-shadow: 0 4px 0 #7E2219;
    cursor: pointer;
  }
  button:active { transform: translateY(3px); box-shadow: 0 1px 0 #7E2219; }
  .tape {
    margin-top: 1.5rem;
    background: var(--paper);
    border: 1px solid var(--edge);
    padding: 1rem 1.1rem 1.4rem;
    font-family: ui-monospace, Consolas, monospace;
    font-variant-numeric: tabular-nums;
    /* torn-paper bottom edge */
    clip-path: polygon(0 0,100% 0,100% calc(100% - 8px),95% 100%,90% calc(100% - 8px),85% 100%,80% calc(100% - 8px),75% 100%,70% calc(100% - 8px),65% 100%,60% calc(100% - 8px),55% 100%,50% calc(100% - 8px),45% 100%,40% calc(100% - 8px),35% 100%,30% calc(100% - 8px),25% 100%,20% calc(100% - 8px),15% 100%,10% calc(100% - 8px),5% 100%,0 calc(100% - 8px));
  }
  @media (prefers-reduced-motion: no-preference) {
    .tape { animation: feed .5s ease-out; }
    @keyframes feed { from { transform: translateY(-12px); opacity: 0; } }
  }
  .tape .row { display: flex; justify-content: space-between; }
  .tape .rule { border-top: 1px dashed var(--edge); margin: .5rem 0; }
  .tape .total { color: var(--red); font-weight: 700; }
  .tape .msg { margin: 0; white-space: pre-wrap; }
</style>
</head>
<body>
  <main class="machine">
    <header>
      <h1>Sum App</h1>
      <p>Built by <strong><?= h($author) ?></strong></p>
      <p class="date"><?= h($today) ?></p>
    </header>

    <form method="POST" action="">
      <div>
        <label for="num1">First number</label>
        <input type="text" id="num1" name="num1" inputmode="decimal" required value="<?= h($num1) ?>">
      </div>
      <div>
        <label for="num2">Second number</label>
        <input type="text" id="num2" name="num2" inputmode="decimal" required value="<?= h($num2) ?>">
      </div>
      <button type="submit" name="submit">Add them up</button>
    </form>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
      <section class="tape" aria-live="polite">
        <div class="row"><span><?= h($num1) ?></span><span>+</span></div>
        <div class="row"><span><?= h($num2) ?></span><span>+</span></div>
        <div class="rule"></div>
        <p class="msg total"><?= $output !== "" ? h($output) : "No response from sum.py. Check that it is in this folder and is readable." ?></p>
      </section>
    <?php endif; ?>
  </main>
</body>
</html>
