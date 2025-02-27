<?php
// Koneksi ke database
$koneksi = mysqli_connect('localhost', 'root', '', 'nasyaa_ukk');
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Tambah Task
if (isset($_POST['add_task'])) {
    $task = $_POST['task'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];
    if (!empty($task) && !empty($priority) && !empty ($due_date)) {
        mysqli_query($koneksi, "INSERT INTO tasks (task, priority, due_date, status) VALUES('$task', '$priority', '$due_date', '0')");
        echo "<script>alert('Data berhasil disimpan');</script>";
    } else {
        echo "<script>alert('Semua kolom harus diisi');</script>";
    }
}

// Edit Task
if (isset($_POST['edit_task'])) {
    $id = $_POST['id'];
    $task = $_POST['task'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];

    mysqli_query($koneksi, "UPDATE tasks SET task='$task', priority='$priority', due_date='$due_date' WHERE id=$id");
    echo "<script>alert('Data berhasil diperbarui');</script>";
    header('Location: index.php');
}

// Menandai Task Selesai
if (isset($_GET['complete'])) {
    $id = $_GET['complete'];
    mysqli_query($koneksi, "UPDATE tasks SET status=1 WHERE id=$id");
    echo "<script>alert('Data berhasil diperbarui');</script>";
    header('Location: index.php');
}

// Menghapus Task
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM tasks WHERE id=$id");
    echo "<script>alert('Data berhasil dihapus');</script>";
    header('Location: index.php');
}

$result = mysqli_query($koneksi, "SELECT * FROM tasks ORDER BY status ASC, priority DESC, due_date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Todo List</title>
    <link rel="stylesheet" href="style.css"><!-- Menyambung ke file css -->
</head>
<body>
    <div class="container mt-2">
        <h2 class="text-center">Catatan Tugas</h2>
        <form method="POST" class="border rounded bg-light p-2">
            <label class="form-label">Nama Task</label>
            <input type="text" name="task" class="form-control" placeholder="Masukan Task Baru" autocomplete="off" autofocus required>
            <label class="form-label">Prioritas</label>
            <select name="priority" class="form-control" required>
                <option value="">--Pilih Prioritas--</option>
                <option value="1">Biasa</option>
                <option value="2">Penting</option>
                <option value="3">Penting Sekali</option>
            </select>
            <label class="form-label">Tanggal</label>
            <input type="date" name="due_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            <button type="submit" class="btn btn-primary w-100 mt-2" name="add_task">Tambah</button>
        </form>

        <hr>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Task</th>
                    <th>Priority</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['task']; ?></td>
                        <td>
                            <?php
                            echo ($row['priority'] == 1) ? "Biasa" : (($row['priority'] == 2) ? "Penting" : "Penting Sekali");
                            ?>
                        </td>
                        <td><?php echo $row['due_date']; ?></td>
                        <td><?php echo ($row['status'] == 0) ? "Belum Selesai" : "Selesai"; ?></td>
                        <td>
                            <?php if ($row['status'] == 0) { ?>
                                <a href="?complete=<?php echo $row['id']; ?>" class="btn btn-success">Selesai</a>
                            <?php } ?>
                            <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger">Hapus</a>
                        </td>
                    </tr>
                <?php }
            } else {
                echo "<tr><td colspan='6' class='text-center'>Tidak ada data</td></tr>";
            }
            ?>
            </tbody>
        </table>

        <?php
        if (isset($_GET['edit'])) {
            $id = $_GET['edit'];
            $edit_query = mysqli_query($koneksi, "SELECT * FROM tasks WHERE id=$id");
            $edit_data = mysqli_fetch_assoc($edit_query);
        ?>
        <h3>Edit Task</h3>
        <form method="POST" class="border rounded bg-light p-2">
            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
            <label class="form-label">Nama Task</label>
            <input type="text" name="task" class="form-control" value="<?php echo $edit_data['task']; ?>" required>
            <label class="form-label">Prioritas</label>
            <select name="priority" class="form-control" required>
                <option value="1" <?php if($edit_data['priority'] == 1) echo "selected"; ?>>Biasa</option>
                <option value="2" <?php if($edit_data['priority'] == 2) echo "selected"; ?>>Penting</option>
                <option value="3" <?php if($edit_data['priority'] == 3) echo "selected"; ?>>Penting Sekali</option>
            </select>
            <label class="form-label">Tanggal</label>
            <input type="date" name="due_date" class="form-control" value="<?php echo $edit_data['due_date']; ?>" required>
            <button type="submit" class="btn btn-primary w-100 mt-2" name="edit_task">Simpan Perubahan</button>
        </form>
        <?php } ?>
    </div>
    <script src="min.js"></script>
</body>
</html>