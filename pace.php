<?php
date_default_timezone_set("America/New_York");
$author = "Gustavo Garcia-Vargas";
$today  = date("l, F j, Y");

$distance = "";
$time = "";
$output = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $distance = trim($_POST["distance"] ?? "");
    $time     = trim($_POST["time"] ?? "");
    // Quote each argument separately, then run the Python backend
    $command = "python3 pace.py " . escapeshellarg($distance) . " " . escapeshellarg($time);
    $output  = trim(shell_exec($command) ?? "");
}

function h($s) { return htmlspecialchars($s, ENT_QUOTES, "UTF-8"); }

// Split the Python output into lines; an "Error:" line means bad input
$lines   = $output === "" ? [] : explode("\n", $output);
$isError = $lines && strpos($lines[0], "Error:") === 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Running Pace Calculator | <?= h($author) ?></title>
<style>
  :root {
    --track: #1F4E8C;
    --lane: #FFFFFF;
    --infield: #E6EEF7;
    --ink: #13233A;
    --muted: #4D607A;
    --flag: #F2B705;
    --error: #B42318;
  }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    min-height: 100vh;
    color: var(--ink);
    font-family: "Segoe UI", system-ui, -apple-system, sans-serif;
    /* lane lines across the background */
    background:
      repeating-linear-gradient(0deg, var(--track) 0 54px, var(--lane) 54px 57px);
    display: grid;
    place-items: center;
    padding: 2rem 1rem;
  }
  main {
    width: 100%;
    max-width: 440px;
    background: var(--infield);
    border-radius: 10px;
    padding: 1.75rem 1.6rem 1.6rem;
    border-left: 10px solid var(--flag);
  }
  h1 {
    margin: 0;
    font-family: "Arial Narrow", "Roboto Condensed", "Segoe UI", sans-serif;
    font-stretch: condensed;
    font-weight: 800;
    font-size: 2.1rem;
    line-height: 1.05;
    letter-spacing: -0.01em;
  }
  .formula { margin: .6rem 0 0; color: var(--muted); font-size: .95rem; line-height: 1.45; }
  .byline { margin: .9rem 0 0; font-size: .95rem; }
  .date { margin: .15rem 0 0; color: var(--muted); font-size: .9rem; }
  form { margin-top: 1.4rem; display: grid; gap: .9rem; }
  label { display: block; font-weight: 600; font-size: .9rem; margin-bottom: .3rem; }
  .hint { font-weight: 400; color: var(--muted); }
  input {
    width: 100%;
    padding: .7rem .8rem;
    font: 1.15rem ui-monospace, Consolas, monospace;
    border: 2px solid #B9C7D8;
    border-radius: 6px;
    background: #fff;
    color: var(--ink);
  }
  input:focus, button:focus-visible { outline: 3px solid var(--flag); outline-offset: 2px; }
  button {
    margin-top: .3rem;
    padding: .8rem;
    font-size: 1.05rem;
    font-weight: 700;
    color: #fff;
    background: var(--track);
    border: none;
    border-radius: 6px;
    cursor: pointer;
  }
  button:hover { background: #173D6E; }
  .result {
    margin-top: 1.4rem;
    padding: 1rem 1.1rem;
    background: #fff;
    border-radius: 6px;
    border: 2px solid var(--track);
  }
  .result .main { font-size: 1.6rem; font-weight: 800; margin: 0; font-variant-numeric: tabular-nums; }
  .result .sub { margin: .2rem 0 0; color: var(--muted); font-variant-numeric: tabular-nums; }
  .result.error { border-color: var(--error); color: var(--error); }
</style>
</head>
<body>
  <main>
    <header>
      <h1>Running Pace Calculator</h1>
      <p class="formula">Finds your pace and speed from distance and finish time:
        pace = time &divide; distance, speed = distance &divide; hours.</p>
      <p class="byline">Code by <strong><?= h($author) ?></strong></p>
      <p class="date"><?= h($today) ?></p>
    </header>

    <form method="POST" action="">
      <div>
        <label for="distance">Distance <span class="hint">(miles)</span></label>
        <input type="text" id="distance" name="distance" inputmode="decimal"
               placeholder="3.1" required value="<?= h($distance) ?>">
      </div>
      <div>
        <label for="time">Time <span class="hint">(minutes, mm:ss, or hh:mm:ss)</span></label>
        <input type="text" id="time" name="time" placeholder="28:45"
               required value="<?= h($time) ?>">
      </div>
      <button type="submit" name="submit">Calculate pace</button>
    </form>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
      <?php if (!$lines): ?>
        <section class="result error" aria-live="polite">
          <p>No response from pace.py. Check that it is in this folder and is readable.</p>
        </section>
      <?php elseif ($isError): ?>
        <section class="result error" aria-live="polite">
          <p><?= h($lines[0]) ?></p>
        </section>
      <?php else: ?>
        <section class="result" aria-live="polite">
          <p class="main"><?= h($lines[0]) ?></p>
          <?php for ($i = 1; $i < count($lines); $i++): ?>
            <p class="sub"><?= h($lines[$i]) ?></p>
          <?php endfor; ?>
        </section>
      <?php endif; ?>
    <?php endif; ?>
  </main>
</body>
</html>
