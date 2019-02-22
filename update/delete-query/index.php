<?php
include('../../../../config/connect.php');
$uang_pajak			= $_POST['uang_pajak'];
$kode_transaksi		= $_POST['kode_transaksi'];
$kode_akun			= $_POST['kode_akun'];
$nama_pajak_patokan = $_POST['nama_pajak_patokan'];
$nama_pajak_spesifik	= $_POST['nama_pajak_spesifik'];
$uang_pajak_spesifik	= $_POST['uang_pajak_spesifik'];
$akun_asal				= $_POST['akun_asal'];
$uang_per_akun = $_POST['uang_per_akun'];

$arr = explode("|", $nama_pajak_spesifik[0], 2);
$nama_pajak_saja = $arr[0];
$pajak_masukkan = "SELECT akun_pajak_pembelian from pajak where nama_pajak='".$nama_pajak_saja."'";
$hasil_query = mysqli_query($connect,$pajak_masukkan);
$data_pajak = mysqli_fetch_array($hasil_query);
$data_pajak['akun_pajak_pembelian'];



//untuk mengambil nilai pajak spesifik
$pajak_masukkan2 = "SELECT * from transaksi where nama_pajak_ori='".$nama_pajak_spesifik[0]."' and kode_akun='".$data_pajak['akun_pajak_pembelian']."' and kode_transaksi='".$kode_transaksi."'";
$hasil_query2 = mysqli_query($connect,$pajak_masukkan2);
$data_pajak2= mysqli_fetch_array($hasil_query2);
$uang_pajak_ambil_db = $data_pajak2['debit'];
$stlh_dikurangi = $uang_pajak_ambil_db-$uang_pajak_spesifik[0];
//----------------------------------------

//untuk mengambil nilai kredit semua total
$total_keseluruhan = "SELECT * from transaksi where kode_akun='".$akun_asal."' and kode_transaksi='".$kode_transaksi."'";
$hasil_query2 = mysqli_query($connect,$total_keseluruhan);
$data_uang_utama= mysqli_fetch_array($hasil_query2);
$total_seluruh_akun = $data_uang_utama['kredit'];
$hasil_kredit = $total_seluruh_akun-($uang_pajak_spesifik[0]+$uang_per_akun);

// echo $uang_pajak_spesifik[0]." ".$uang_per_akun." ".$total_seluruh_akun." ".$hasil_kredit;
//----------------------------------------


//untuk mengurangi akun khusus pajak, contoh ppn masukkan
$update_kirim_uang = "UPDATE `transaksi` set `debit`='".$stlh_dikurangi."' where nama_pajak_ori='".$nama_pajak_spesifik[0]."' and kode_akun='".$data_pajak['akun_pajak_pembelian']."' and kode_transaksi='".$kode_transaksi."' ";
mysqli_query($connect, $update_kirim_uang);
//------------------------------------------------------

//untuk mengurangi akun utama kredit, contoh 1-10002 | Rekening Bank
$update_kredit_utama = "UPDATE `transaksi` set `kredit`='".$hasil_kredit."' where kode_akun='".$akun_asal."' and kode_transaksi='".$kode_transaksi."' ";
mysqli_query($connect, $update_kredit_utama);
//------------------------------------------------------

//untuk menjumlah ulang semua uang di tabel transaksi akun
$update_tbl_trnsaksi_akun = "UPDATE `transaksi_akun` set `kredit`='".$hasil_kredit."' where  kode_transaksi='".$kode_transaksi."' ";
mysqli_query($connect, $update_tbl_trnsaksi_akun);
//--------------------------------------------------------


$sql_hapus_pajak = "DELETE FROM `transaksi` where kode_akun='".$kode_akun."' and harga_pajak='".$uang_pajak."' and `kode_transaksi`= '".$kode_transaksi."' ";
mysqli_query($connect, $sql_hapus_pajak);
//pertama ambil data lalu kurangin pajak dengan akun yang bersangkutan di transaksi lalu kurangin di total  , 

//delete ketika pajak sudah 0
$sql_menghapus_null= "DELETE FROM `transaksi` where kode_akun='".$data_pajak['akun_pajak_pembelian']."' and `kode_transaksi`= '".$kode_transaksi."' and `debit`=0";
mysqli_query($connect, $sql_menghapus_null);

//delete ketika kredit utama sudah 0
$sql_kredit_null= "DELETE FROM `transaksi` where `kode_akun`='".$akun_asal."' and `kode_transaksi`= '".$kode_transaksi."' and `kredit`=0";
mysqli_query($connect, $sql_kredit_null);
// 

$sql_penghapus_transaksi = "DELETE FROM `transaksi_akun` where not exists (select null from `transaksi` where `kode_transaksi` = '".$kode_transaksi."' )"; 
mysqli_query($connect, $sql_penghapus_transaksi);
?>