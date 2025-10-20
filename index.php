<?php
$folder = __DIR__ . '/';
if (!is_dir($folder)) {
mkdir($folder, 0775, true);
}
$message = "";
$selected = $_POST['selected'] ?? '';
$newname = $_POST['newname'] ?? '';
$newfile = $_POST['newfile'] ?? '';
$content = $_POST['content'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajaxRename'])) {
$old = $_POST['old'] ?? '';
$new = $_POST['new'] ?? '';
if ($old && $new && file_exists("$folder/$old")) {
rename("$folder/$old", "$folder/$new");
echo "Renamed to $new";
}
exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
if (isset($_POST['create']) && $newfile) {
$path = "$folder/$newfile";
if (!file_exists($path)) {
file_put_contents($path, "");
$message = "File '$newfile' created.";
} else {
$message = "File already exists.";
}
}
if (isset($_POST['save']) && $selected) {
file_put_contents("$folder/$selected", $content);
$message = "File '$selected' saved.";
}
if (isset($_POST['delete']) && $selected) {
unlink("$folder/$selected");
$message = "File '$selected' deleted.";
$selected = '';
}
}
$newfolder = $_POST['newfolder'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createFolder']) && $newfolder) {
$folderPath = "$folder/$newfolder";
if (!is_dir($folderPath)) {
mkdir($folderPath, 0775, true);
$message = "Folder '$newfolder' dibuat.";
} else {
$message = "Folder sudah ada.";
}
}
$folders = array_filter(scandir($folder), fn($f) => is_dir("$folder/$f") && $f !== '.' && $f !== '..');
$files = array_filter(scandir($folder), fn($f) => is_file("$folder/$f"));
?>

<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
<title>SvgEditor</title>
<style>
*{box-sizing:border-box;margin:0;padding:0;}
:root{width:100vw;height:70vh;left:0;margin:0;padding: 0; overflow:hidden;}
body{background:#fff;width:100vw;height:70vh;border:4px dashed #ccc;margin:0;padding: 0; font-family: sans-serif; overflow:scroll;}
.content{display:flex;width:180vw;height:70vh;}
.left{flex:1;width:100%;display: background-color: #f0f0f0;overflow:auto;}
.right{flex:1;width:100%;display: background-color: #f0f0f0;background:#f0f0f2;height:100%;overflow:auto;border-left:4px dashed #ccc;}
.left textarea{width:200%;height:100%;padding:10px;}
.right iframe{width:100%;height:100%;}
nav{
display:flex;
width:100%;
}
nav button,nav input[type="text"]{
width:100%;
text-align:center;
flex:1;
padding: 5px 0;
cursor: pointer;
border-radius: 4px;
border:2px solid #ccc;
font-size:16px;
}
.notif{padding:4px;}
#list{width:100%;height:100px;white-space:nowrap;overflow:scroll;border:1px solid }
label{margin:2px;padding:5px;display:block;background:#0f0f0f;color:#eee;}
#tool{position:fixed;left:0;bottom:0;}
</style>
<script src='jquery.min.js'></script>
</head>
<body>
<div class="content">
<div class="left">
<textarea id="editor" name="content"><?= htmlspecialchars($content) ?></textarea>
</div>
<div class="right">
<iframe id="preview"></iframe>
</div></div>
<form id="tool" method="post" id="fileForm">
<input type="hidden" name="selected" id="selectedFile">
<div id="list">
<?php foreach ($files as $file): ?>
<label>
<input type="radio" name="selected" value="<?= htmlspecialchars($file) ?>"
<?= $selected === $file ? 'checked' : '' ?>
onclick="loadFile('<?= htmlspecialchars($file) ?>')">
<?= htmlspecialchars($file) ?>
</label>
<?php endforeach; ?>
</div>
<hr>
<nav>
<button type="submit" name="delete" onclick="return confirm('Yakin hapus?')">Hapus</button>
<input type="text" name="newname" id="renameInput" placeholder="Rename" autocomplete="off" oninput="autoRename(this.value)">
<input type="text" name="newfile" placeholder="Nama file baru" autocomplete="off" >
<button type="submit" name="create">Buat</button>
</nav>
</form>
<?php if ($message): ?>
<p class="notif"><strong>✅ <?= htmlspecialchars($message) ?></strong></p>
<?php endif; ?>
<script>
const folder = '<?= basename($folder) ?>/';
function loadFile(filename) {
$('#selectedFile, #renameInput').val(filename);
$.get(folder + filename, function(text) {
$('#editor').val(text);
$('#preview').attr('srcdoc',text);
});
}
$('#editor').on('input', function() {
const content = $(this).val();
const filename = $('#selectedFile').val();
if (!filename) return;
$('#preview').attr('srcdoc',content);
$.post('', {
save: 1,
selected: filename,
content: content
}, function(response) {
console.log('Auto-saved:', response);
});
});
function autoRename(newName) {
const oldName = $('#selectedFile').val();
if (!oldName || !newName || oldName === newName) return;
$.post('', {
ajaxRename: 1,
old: oldName,
new: newName
}, function(msg) {
console.log(msg);
$('#selectedFile').val(newName);
updateRadioLabel(oldName, newName);
});
}
function updateRadioLabel(oldName, newName) {
$('input[type=radio]').each(function() {
if (this.value === oldName) {
this.value = newName;
this.nextSibling.textContent = newName;
}
});
}
</script>
</body>
</html>