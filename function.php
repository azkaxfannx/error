<?php
    class DatabaseFunction {
        public static function tampiltDB($listDBPegawai) {
            if (is_dir($listDBPegawai)) {
                $files = opendir($listDBPegawai);
    
                if ($files) {
                    $isEmpty = true;
                    echo 'Database yang sudah dibuat: <br><br>';
                    
                    while (($file = readdir($files)) !== false) {
                        if ($file != "." && $file != "..") {
                            echo "$file " . "<form method='post'>";
                            echo "<input type='hidden' name='fileToDelete' value='$file'>";
                            echo "<button type='submit' name='hapusDB'>Hapus</button></form><br>";
                            $isEmpty = false;
                        }
                    }
            
                    closedir($files);
            
                    if ($isEmpty) {
                        echo 'Belum ada database yang dibuat!';
                    }
                }
            }
        }

        public static function tampilDBPegawai($listDBPegawai) {
            if(isset($_POST['submitPilihanDB'])) {
                $_SESSION['selectedDatabase'] = $_POST['pilihDatabase'];
                $file = $listDBPegawai . $_SESSION['selectedDatabase'];
            
                    if($file) {
                        $databasePilih = fopen($file, 'r');
                        $arrData = [];
            
                        $isEmpty = true;
                        
                        $i = 0;
                        while(!feof($databasePilih)) {
                            $arrData[$i] = trim(fgets($databasePilih));
                            if(!empty($arrData[$i])) {
                                $isEmpty = false;
                            }
                            $i++;
                        }
            
                        fclose($databasePilih);
            
                        if(!$isEmpty) {
                            foreach($arrData as $isiDB) {
                                $_SESSION['status'] = "$isiDB<br>";
                                echo $_SESSION['status'];
                            }
                        } else {
                            $_SESSION['status'] = 'Database masih kosong!';
                            echo $_SESSION['status'];
                        }
                    }
            } else {
                if(isset($_SESSION['status'])) {
                    echo $_SESSION['status'];
                } else {
                    echo 'Database belum dipilih!';
                }
            }
        }

        public static function tampilDBSelect($listDBPegawai) {
            if (is_dir($listDBPegawai)) {
                $files = opendir($listDBPegawai);
    
                if ($files) {
                    $isEmpty = true;
                    
                    while (($file = readdir($files)) !== false) {
                        if ($file != "." && $file != "..") {
                            echo "<option value='$file'";
                            echo isset($_SESSION['selectedDatabase']) && $_SESSION['selectedDatabase'] === $file ? ' selected' : null;
                            echo ">$file</option>";
                            $isEmpty = false;
                        }
                    }
            
                    closedir($files);
            
                    if ($isEmpty) {
                        echo "<option value='$file' disabled>";
                        echo "Belum ada database yang dibuat!";
                        echo "</option>";
                    }
                }
            }
        }

        public static function buatDB() {
            if(isset($_POST['submitNamaDatabasePegawai'])) {
                if($_POST['namaDatabasePegawai']) {
                    $namaFile = $_POST['namaDatabasePegawai'] . '.txt';
                    $namaDB = "../database/db_pegawai/" . $_POST['namaDatabasePegawai'] . ".txt";
        
                    if(!file_exists($namaDB)) {
                        $file = fopen($namaDB, 'w');
                        echo "<script>alert('Database $namaFile berhasil dibuat!')</script>";
                        echo "<meta http-equiv='refresh' content='0; url=./buatDatabase.php'>";
                    } else {
                        echo "<script>alert('Database $namaFile sudah ada!')</script>";
                    }
                } else {
                    echo 'Nama database belum diisi!';
                }
            }
        }

        public static function hapusDB($listDBPegawai) {
            if (isset($_POST['hapusDB'])) {
                $fileToDelete = $_POST['fileToDelete'];
                unlink($listDBPegawai . $fileToDelete);
                echo "<script>alert('Database $fileToDelete berhasil dihapus')</script>";
                echo "<meta http-equiv='refresh' content='0; url=./buatDatabase.php'>";
            }
        }
    }

    class UserFunction {
        public static function checkUser() {
            if(isset($_SESSION['user'])) {
                return true;
            }
    
            return false;
        }

        public static function rejectUser() {
            if(!self::checkUser()) {
                header('Location: ./login.php');
                die();
            }
        }

        public static function loginUser() {
            $db_user = fopen("..\database\db_user\username.txt", 'r');
            $db_password = fopen("..\database\db_user\password.txt", 'r');

            if(isset($_POST['submitLogin'])) {
                if(!empty($_POST['username'] && !empty($_POST['password']))) {
                    $username = trim(fgets($db_user));
                    $password = trim(fgets($db_password));

                    if($_POST['username'] == $username && $_POST['password'] == $password) {
                        $_SESSION['success'] = 'Login berhasil!';
                        $_SESSION['user'] = compact('username', 'password');
                        echo "<script>alert('" . $_SESSION['success'] . "')</script>";
                        echo "<meta http-equiv='refresh' content='0; url=./dashboard.php'>";
                    } else {
                        $_SESSION['error'] = 'Username atau password salah!';
                        echo "<script>alert('" . $_SESSION['error'] . "')</script>";
                    }
                } else {
                    $_SESSION['kosong'] = 'Username atau password belum diisi!';
                    echo "<script>alert('" . $_SESSION['kosong'] . "')</script>";
                }
            }
            if(isset($_SESSION['logout'])) {
                session_destroy();
            }
        
            fclose($db_user);
            fclose($db_password);
        }

        public static function logoutUser() {
            if(isset($_POST['logout'])) {
                $_SESSION['logout'] = 'Berhasil logout!';
                echo "<script>alert('" . $_SESSION['logout'] . "')</script>";
                echo "<meta http-equiv='refresh' content='0; url=./login.php'>";
            }
        }
    }

    class Fiture {
        public static function balikDashboard() {
            if(isset($_POST['balikDashboard'])) {
                header('Location: ./dashboard.php');
                die();
            }
        }
    }

    class Pegawai {
        public static function validasiTambahDataPegawai($listDBPegawai) {
            if (isset($_POST['submitDataPegawai'])) {
                $_SESSION['status'] = $_POST['status'];
                $tampilForm = false;
        
                if (!empty($_POST['golongan']) && !empty(array_filter($_POST))) {
                    echo "<script>alert('Data pegawai berhasil dikirim!')</script>";
        
                    self::tambahDataPegawai($listDBPegawai);

                    unset($_SESSION['selectedDatabase'], $_SESSION['keterangan']);
                } else {
                    echo "<script>alert('Gagal! Ada data yang belum diisi!')</script>";
                }
            }
        }
        
        public static function tambahDataPegawai($listDBPegawai) {
            $database = $listDBPegawai . $_SESSION['selectedDatabase'];
        
            $simpan = fopen($database, 'a');
        
            fwrite($simpan, $_POST['nik'] . ',' . $_POST['nama'] . ',' 
            . $_POST['alamat'] . ',' . $_POST['unit'] . ',' . $_POST['golongan'] 
            . ',' . $_POST['jumlahAnak'] . ',' . $_POST['masuk'] . ',' . $_POST['jamKerja'] . PHP_EOL);

            fclose($simpan);
        }
    }
?>