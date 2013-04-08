<?php
$author = 'pmilan';
$since = 'Fri Apr 1 00:00:00 2013 +0200';
$until = 'Fri Apr 6 00:00:00 2013 +0200';
$workspace = '/home/pity/workspace';

$projects = scandir($workspace);
$black_list = array(
  '..',
  'gitlogs',
);
$git_projects = array();
foreach ($projects as $project) {
  if (is_dir(implode('/', array($workspace , $project, '.git')))) {
    if (!in_array($project, $black_list)) {
      $git_projects[] = $project;
    }
  }
}

$results_by_day = array();
foreach ($git_projects as $project) {
  $cmd = 'cd ../' . $project . ' && git log --reverse --author='. $author .' --since="' . $since . '" --until="' . $until . '" --no-merges --all';
  $result = shell_exec($cmd);
  if ($result) {
    $commits = explode('commit ', $result);
    foreach ($commits as $commit) {
      if (!empty($commit)) {
        $pos = strpos($commit, "\n\n");
        $raw = trim(substr($commit, 0, $pos));
        $raw_pos = strpos($raw, 'Date:');
        $date = trim(str_replace('Date:', '', substr($raw, $raw_pos, strlen($commit))));

        $message = trim(substr($commit, $pos, strlen($commit)));
        $time = strtotime($date);
        $date_formatted = date('Y-m-d H:i:s', $time);
        $day = date('Y-m-d', $time);
        $results_by_day[$day][] = array(
          'date' => $date_formatted,
          'message' => $message,
          'project' => $project,
        );
      }
    }
  }
}
?>

<style type="text/css">
  table {
    border-width: 2px;
    border-spacing: 2px;
    border-style: solid;
    border-color: black;
    border-collapse: collapse;
    background-color: white;
  }
  table th {
    border-width: 2px;
    padding: 1px;
    border-style: inset;
    border-color: gray;
    background-color: white;
  }
  table td {
    border-width: 2px;
    padding: 1px;
    border-style: inset;
    border-color: gray;
    background-color: white;
  }
</style>

<strong>Author</strong>: <?php print $author; ?>
<br />
<strong>Since</strong>: <?php print $since; ?>
<br />
<strong>Until</strong>: <?php print $until; ?>
<br />

<table>
<?php foreach($results_by_day as $day => $commits) : ?>
  <tr>
    <td>
      <?php print $day; ?>
    </td>
    <td>
      <table>
        <?php foreach ($commits as $data) : ?>
        <tr>
          <td>
            <?php print $data['date']; ?>
          </td>
          <td>
            <?php print $data['project']; ?>
          </td>
          <td>
            <?php print $data['message']; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>
    </td>
  </tr>
<?php endforeach; ?>
</table>