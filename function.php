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
            if (isset($_POST["submitPilihanDB"]) && isset($_POST['pilihDatabase'])) {
               $_SESSION['selectedDatabase'] = $_POST['pilihDatabase'];
            }
            
            if(isset($_SESSION['selectedDatabase'])) {                
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
                                if(!empty($isiDB)) {
                                    $dataPegawai = explode(',', $isiDB);
                                    $_SESSION['status'] = $dataPegawai[0] . ', ' . $dataPegawai[1] . ', ' . $dataPegawai[3];
                                    $_SESSION['selectedNIK'] = $dataPegawai[0];
                                    echo "<form method='post'>";
                                    echo $_SESSION['status'] . ' ';
                                    echo "<button type='submit' name='hapusDataPegawai'>Hapus Data Pegawai</button>" . ' ';
                                    echo "<button type='submit' name='ubahDataPegawai'>Ubah Data Pegawai</button>" . ' ';
                                    echo "<button type='submit' name='detailDataPegawai'>Detail Data Pegawai</button>";
                                    echo "</form>";
                                    echo "<br>";
                                }
                            }
                        } else {
                            $_SESSION['status'] = 'Database masih kosong!';
                            echo $_SESSION['status'];
                        }
                    } else {
                        echo 'Database belum dipilih!';
                    }
            } else {
                echo 'Database belum dipilih!';
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
            } else {
                return false;
            }
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
                unset($_SESSION['selectedDatabase'], $_SESSION['status'], $_SESSION['selectedNIK']);
                header('Location: ./dashboard.php');
                die();
            }
        }
    }

    class Pegawai {
        public static function validasiTambahDataPegawai($listDBPegawai) {
            $nikTerdaftar = [];
            if (isset($_POST['submitDataPegawai']) && isset($_SESSION['selectedDatabase'])) {
                $tampilForm = false;
                $path = $listDBPegawai . $_SESSION['selectedDatabase'];
                $database = fopen($path, 'r');
                $nik = $_POST['nik'];

                $isEmpty = true;
                $arrData = [];
                $i = 0;
                while(!feof($database)) {
                    $arrData[$i] = trim(fgets($database));
                    if(!empty($arrData[$i])) {
                        $isEmpty = false;
                    }
                    $i++;
                }

                fclose($database);
            
                if(!$isEmpty) {
                    foreach($arrData as $isiDB) {
                        if(!empty($isiDB)) {
                            $dataPegawai = explode(',', $isiDB);
                            $nikTerdaftar[] = $dataPegawai[0];
                        }
                    }
                }
                    if(!in_array($nik, $nikTerdaftar)) {
                        if(isset($_SESSION['selectedDatabase'])) {
                            if (!empty($_POST['golongan']) && !empty(array_filter($_POST))) {
                                self::tambahDataPegawai($listDBPegawai);
            
                                echo "<script>alert('Data pegawai berhasil dikirim!')</script>";

                                unset($_SESSION['selectedDatabase'], $_SESSION['status']);
                            } else {
                                echo "<script>alert('Gagal! Ada data yang belum diisi!')</script>";
                            }
                        } else {
                            echo "<script>alert('Database belum dipilih!')</script>";
                        }
                    } else {
                        echo "<script>alert('NIK sudah terdaftar di database!')</script>";
                    }
                }
            }
        
        public static function tambahDataPegawai($listDBPegawai) {
            $database = $listDBPegawai . $_SESSION['selectedDatabase'];
        
            $nik = $_POST['nik'];
            $nama = $_POST['nama'];
            $alamat = $_POST['alamat'];
            $unit = $_POST['unit'];
            $golongan = $_POST['golongan'];
            $jumlahAnak = $_POST['jumlahAnak'];
            $masuk = $_POST['masuk'];
            $jamKerja = $_POST['jamKerja'];

            $dataPegawai = [
                'nik' => $nik,
                'nama' => $nama,
                'alamat' => $alamat,
                'unit' => $unit,
                'golongan' => $golongan,
                'jumlahAnak' => $jumlahAnak,
                'masuk' => $masuk,
                'jamKerja' => $jamKerja,
            ];

            $pegawaiCSV = implode(',', $dataPegawai);

            $file = fopen($database, 'a');
        
            fwrite($file, $pegawaiCSV . PHP_EOL);

            fclose($file);
        }

        public static function hapusDataPegawai($listDBPegawai) {
            if(isset($_POST['hapusDataPegawai']) && isset($_SESSION['selectedNIK'])) {
                echo "<script>alert('Data pegawai berhasil dihapus!')</script>";

                $database = $listDBPegawai . $_SESSION['selectedDatabase'];
                
                $selectedNIK = $_SESSION['selectedNIK'];

                $path = file($database);

                $file = fopen($database, 'w');

                foreach($path as $db) {
                    $dataPegawai = explode(',', $db);

                    if($dataPegawai[0] != $selectedNIK) {
                        fwrite($file, $db);
                    }
                }
                fclose($file);
            }
        }

        public static function ubahDataPegawai($listDBPegawai) {
            if(isset($_POST['ubahDataPegawai']) && isset($_SESSION['selectedNIK'])) {
                $selectedNIK = $_SESSION['selectedNIK'];
                $database = $listDBPegawai . $_SESSION['selectedDatabase'];
                $file = fopen($database, 'r+');
                
                $dataPegawai = null;
                $posisiData = 0;

                while (!feof($file)) {
                    $db = trim(fgets($file));
                    $pegawai = explode(',', $db);
        
                    if ($pegawai[0] == $selectedNIK) {
                        $dataPegawai = $pegawai;
                        $posisiData = ftell($file);
                        break;
                    }
                }

                fclose($file);

                if($dataPegawai) {
                    echo "<form method='post'>";
                        echo "<input type='number' name='nik' placeholder='NIK' value='$dataPegawai[0]' required>";
                        echo "<br><br>";
                        echo "<input type='text' name='nama' placeholder='Nama' value='$dataPegawai[1]' required>";
                        echo "<br><br>";
                        echo "<textarea name='alamat' cols='30' rows='10' placeholder='Alamat' required>$dataPegawai[2]</textarea>";
                        echo "<br><br>";
                        echo "<input type='text' name='unit' placeholder='Unit' value='$dataPegawai[3]' required>";
                        echo "<br><br>";
                        echo "<label>Golongan</label>";
                        echo "<br>";
                        echo "<input type='radio' name='golongan' value='IV-A'" . ($dataPegawai[4] == 'IV-A' ? 'checked' : '') . ">";
                        echo "<label for='IV-A'>IV-A</label>";
                        echo "<br>";
                        echo "<input type='radio' name='golongan' value='IV-B'" . ($dataPegawai[4] == 'IV-B' ? 'checked' : '') . ">";
                        echo "<label for='IV-B'>IV-B</label>";
                        echo "<br>";
                        echo "<input type='radio' name='golongan' value='IV-C'" . ($dataPegawai[4] == 'IV-C' ? 'checked' : '') . ">";
                        echo "<label for='IV-C'>IV-C</label>";
                        echo "<br>";
                        echo "<input type='radio' name='golongan' value='III-A'" . ($dataPegawai[4] == 'III-A' ? 'checked' : '') . ">";
                        echo "<label for='III-A'>III-A</label>";
                        echo "<br>";
                        echo "<input type='radio' name='golongan' value='III-B'" . ($dataPegawai[4] == 'III-B' ? 'checked' : '') . ">";
                        echo "<label for='III-B'>III-B</label>";
                        echo "<br>";
                        echo "<input type='radio' name='golongan' value='III-C'" . ($dataPegawai[4] == 'III-C' ? 'checked' : '') . ">";
                        echo "<label for='III-C'>III-C</label>";
                        echo "<br><br>";
                        echo "<input type='number' name='jumlahAnak' placeholder='Jumlah Anak' value='$dataPegawai[5]' required>";
                        echo "<br><br>";
                        echo "<input type='number' name='masuk' placeholder='Masuk (Hari)' value='$dataPegawai[6]' required>";
                        echo "<br><br>";
                        echo "<input type='number' name='jamKerja' placeholder='Jam Kerja' value='$dataPegawai[7]' required>";
                        echo "<br><br>";
                        echo "<button type='submit' name='submitUbahDataPegawai'>Ubah Data Pegawai!</button>";
                    echo "</form>";
                }
                if(isset($_POST['submitUbahDataPegawai'])) {
                    echo "<script>alert('Data pegawai berhasil diubah!')</script>";

                    $newNik = $_POST['nik'];
                    $newName = $_POST['nama'];
                    $newAlamat = $_POST['alamat'];
                    $newUnit = $_POST['unit'];
                    $newGolongan = $_POST['golongan'];
                    $newJumlahAnak = $_POST['jumlahAnak'];
                    $newMasuk = $_POST['masuk'];
                    $newJamKerja = $_POST['jamKerja'];

                    $newDataPegawai = [
                        'nik' => $newNik,
                        'nama' => $newName,
                        'alamat' => $newAlamat,
                        'unit' => $newUnit,
                        'golongan' => $newGolongan,
                        'jumlahAnak' => $newJumlahAnak,
                        'masuk' => $newMasuk,
                        'jamKerja' => $newJamKerja,
                    ];

                    $newDataCSV = implode(',', $newDataPegawai);

                    $file = fopen($database, 'r+');
                    fseek($file, $posisiData);
                    fwrite($file, $newDataCSV . PHP_EOL);
                    fclose($file);
                }
            }
        }
    }
?>
