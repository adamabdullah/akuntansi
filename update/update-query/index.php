<?php
include('../../../../config/connect.php');
$akun_bayar				= $_POST['akun_bayar'];
$total_semuanya	= $_POST['total_uang_tnpa_koma'];
$tgl_transaksi			= $_POST['tgl_transaksi'];
// $tgl_tempo				=
$cara_pembayaran		= $_POST['cara_pembayaran'];
$nomor_transaksi				= $_POST['no_biaya'];
$penerima				= $_POST['penerima'];
$cara_pembayaran		= $_POST['cara_pembayaran'];
$memo					= $_POST['memo'];
$uang_per_akun_lama		= $_POST['uang_per_akun_lama'];
$uang_pajak_perakun_lama= $_POST['uang_pajak_perakun_lama'];
$nama_pajak_lama		= $_POST['nama_pajak_lama'];
// $deskripsi_lama			= $_POST['deskripsi_lama'];
$akun_lama   			= $_POST['akun_lama'];
$kode_rahasia 			= $_POST['kode_rahasia'];
$nama_pajak_total		= $_POST['nama_pajak_total'];
$uang_pajak_total		= $_POST['uang_pajak_total'];
$akun_patokan			= $_POST['akun_patokan'];
$nama_pajak_patokan		= $_POST['nama_pajak_patokan'];

// print_r($nama_pajak_patokan);
// print_r($nama_pajak_total);
$sql = "UPDATE `transaksi_produk` SET `pelanggan`='".$penerima."', `tgl_transaksi`='".$tgl_transaksi."', `kode`='".$nomor_transaksi."', `memo`='".$memo."', `total`='".$total_semuanya."' WHERE kode='".$kode_rahasia."'";
// mysqli_query($connect, $sql);

$hasil = array(); //-----------------------------pertama jadikan object json dulu
foreach ($nama_pajak_total as $id => $key) 
{
    $hasil[$id] = array(
        'nama'  => $nama_pajak_total[$id],
        'uang' => $uang_pajak_total[$id],
    );
}

$amount = array(); //---------------------------------setelah itu cari mana yg sama
foreach($hasil as $bank) {  
    $index = bank_exists($bank['nama'], $amount);
    if ($index < 0) 
    {
         $amount[] = $bank;
    }
    else 
    {
        $amount[$index]['uang']+=$bank['uang'];    
    }
}

function bank_exists($bankname, $array) {
    $result2 = -1;
    for($i=0; $i<sizeof($array); $i++) 
    {
        if ($array[$i]['nama'] == $bankname) {
            $result2 = $i;
            break;
        }
    }
    return $result2;
}

//--------------------------
$hasil2 = array(); //-----------------------------menggabungkan nama pajak lama
foreach ($nama_pajak_patokan as $id => $key) 
{
    $hasil2[$id] = array(
        'nama'  => $nama_pajak_patokan[$id],
    );
}

$amount2 = array(); //---------------------------------setelah itu cari mana yg sama
foreach($hasil2 as $bank) {  
    $index2 = nama_pajak_lama($bank['nama'], $amount2);
    if ($index2 < 0) 
    {
         $amount2[] = $bank;
    }
    else 
    {
    }
}

function nama_pajak_lama($bankname, $array) {
    $result21 = -1;
    for($i=0; $i<sizeof($array); $i++) 
    {
        if ($array[$i]['nama'] == $bankname) {
            $result21 = $i;
            break;
        }
    }
    return $result21;
}

$nama_pajak3 = array_column($amount2, 'nama'); //nama_pajak_patokan
$uang_pajak3 = array_column($amount2, 'uang'); //nama_pajak_patokan

$uang_pajak2 = array_column($amount, 'uang'); //nama_pajak_total
$nama_pajak2 = array_column($amount, 'nama');


$query = "SELECT no as kode_child from transaksi_akun where kode_transaksi='".$nomor_transaksi."'";
$hasil = mysqli_query($connect,$query);
$data = mysqli_fetch_array($hasil);
$kodeBarang = $data['kode_child'];
$noUrut = (int)$kodeBarang;

$sql2 = "UPDATE `transaksi` SET `kode_transaksi`= '".$nomor_transaksi."', `kredit`='".$total_semuanya."', `no`='".$noUrut."', `syarat_pembayaran`='".$cara_pembayaran."' WHERE kode_akun='".$akun_bayar."' AND kode_transaksi='".$nomor_transaksi."'";
    mysqli_query($connect, $sql2); 

//nama pajak3 = patokan(old)
//nama pajak2 = baru(new)
$result = array_diff($nama_pajak3, $nama_pajak2); 
$result2 = array_diff($nama_pajak2, $nama_pajak3); 

$result_final1 = array_values($result);
$result_final2 = array_values($result2);

$jumlah = count($result_final1);//(old)
$jumlah2 = count($result_final2);//(new)


if($jumlah2 != 0)
{
	foreach ($nama_pajak_lama as $key2 => $value) 
	{
		$tmp_akun = '';
		$nama_akun_tmp = '';
		$uang_akun_tmp = '';
		$uang_pajak_tmp = '';
		foreach ($result_final2 as $key => $value) 
		{
			if($nama_pajak_lama[$key2] == $result_final2[$key])
			{
				$tmp_akun = $akun_lama[$key2];
				$nama_pajak_tmp = $nama_pajak_lama[$key2];
				$uang_pajak_tmp = $uang_pajak_perakun_lama[$key2];
				$uang_akun_tmp = $uang_per_akun_lama[$key2];

				$arr = explode("|", $nama_pajak_tmp, 2);
				$nama_pajak_saja = $arr[0];
				$pajak_masukkan1 = "SELECT akun_pajak_pembelian from pajak where nama_pajak='".$nama_pajak_saja."'";
			    $hasil_query1 = mysqli_query($connect,$pajak_masukkan1);
			    $data_pajak1 = mysqli_fetch_array($hasil_query1);


				$sql_insert_pajak_baru = "INSERT INTO `transaksi`(`kode_transaksi`,`kode_akun`, `kolom`, `debit`, `no`, `nama_pajak_ori`) VALUES('".$nomor_transaksi."', '".$data_pajak1['akun_pajak_pembelian']."', 'biaya', '".$uang_pajak_tmp."', '".$noUrut."', '".$nama_pajak_tmp."')";
				// mysqli_connect($connect, $sql_insert_pajak_baru);
				if (mysqli_connect_errno()) 
				{
				    printf("Connect failed: %s\n", mysqli_connect_error());
				    exit();
				}

				if (!mysqli_query($connect, $sql_insert_pajak_baru)) {
				    printf("Errormessage: %s\n", mysqli_error($connect));
				}
			}	
		}
		
	}	
}

if($jumlah != 0)
{
	foreach ($result_final1 as $key => $value) 
	{
		//untuk menghapus yang beda dengan pajak patokan
		$arr = explode("|", $result_final1[$key], 2);
		$nama_pajak_saja = $arr[0];
		$pajak_masukkan1 = "SELECT akun_pajak_pembelian from pajak where nama_pajak='".$nama_pajak_saja."'";
	    $hasil_query1 = mysqli_query($connect,$pajak_masukkan1);
	    $data_pajak1 = mysqli_fetch_array($hasil_query1);

		$query_pajak_seluruhan = "DELETE from transaksi where nama_pajak_ori='".$result_final1[$key]."'  AND kode_transaksi='".$nomor_transaksi."' and  kode_akun='".$data_pajak1['akun_pajak_pembelian']."'";
	    mysqli_query($connect,$query_pajak_seluruhan);
	}

	foreach ($nama_pajak2 as $key => $value)  //pajak
	{
	    $arr = explode("|", $nama_pajak2[$key], 2);
	    $nama_pajak_saja = $arr[0];
	    $pajak_masukkan = "SELECT akun_pajak_pembelian from pajak where nama_pajak='".$nama_pajak_saja."'";
	    $hasil_query = mysqli_query($connect,$pajak_masukkan);
	    $data_pajak = mysqli_fetch_array($hasil_query);

	    $query_kode = "SELECT nama_pajak_ori from transaksi where kode_transaksi='".$nomor_transaksi."' and kode_akun='".$data_pajak['akun_pajak_pembelian']."' and nama_pajak_ori='".$nama_pajak2[$key]."'";
	    $hasil_query2 = mysqli_query($connect,$query_kode);
	    $nama_pajak = mysqli_fetch_array($hasil_query2);

	    $sql_pajak = "UPDATE `transaksi` SET `debit`= '".$uang_pajak2[$key]."' where `kode_transaksi`= '".$nomor_transaksi."' and `kode_akun`='".$data_pajak['akun_pajak_pembelian']."' and `nama_pajak_ori`='".$nama_pajak2[$key]."'";
	    mysqli_query($connect, $sql_pajak);
	}
	foreach ($akun_lama as $key => $value)  //akun
	{
		echo $akun_lama[$key]." <= akun lama \n";
		echo $akun_patokan[$key]." <= akun patokan \n \n";
		$arr = explode("|", $nama_pajak_lama[$key], 2);
	    $nama_pajak_saja = $arr[0];
	    $pajak_masukkan = "SELECT akun_pajak_pembelian from pajak where nama_pajak='".$nama_pajak_saja."'";
	    $hasil_query = mysqli_query($connect,$pajak_masukkan);
	    $data_pajak = mysqli_fetch_array($hasil_query);

	    $sql_kirim11 = "UPDATE `transaksi` set `kode_transaksi`= '".$nomor_transaksi."', `kode_akun`='".$akun_lama[$key]."', `debit`='".$uang_per_akun_lama[$key]."', harga_pajak='".$uang_pajak_perakun_lama[$key]."', nama_pajak='".$data_pajak['akun_pajak_pembelian']."',nama_pajak_ori='".$nama_pajak_lama[$key]."' where kode_akun='".$akun_patokan[$key]."' AND kode_transaksi='".$nomor_transaksi."'";
	    mysqli_query($connect, $sql_kirim11);
	}
	
}




// if($jumlah > $jumlah2)
// {
// 	foreach ($result_final1 as $key => $value) 
// 	{
// 		//untuk menghapus yang beda dengan pajak patokan
// 		$arr = explode("|", $result_final1[$key], 2);
// 		$nama_pajak_saja = $arr[0];
// 		$pajak_masukkan1 = "SELECT akun_pajak_pembelian from pajak where nama_pajak='".$nama_pajak_saja."'";
// 	    $hasil_query1 = mysqli_query($connect,$pajak_masukkan1);
// 	    $data_pajak1 = mysqli_fetch_array($hasil_query1);

// 		$query_pajak_seluruhan = "DELETE from transaksi where nama_pajak_ori='".$result_final1[$key]."'  AND kode_transaksi='".$nomor_transaksi."' and  kode_akun='".$data_pajak1['akun_pajak_pembelian']."'";
// 	    mysqli_query($connect,$query_pajak_seluruhan);

// 	    //mengganti pajak 
// 	     $sql_pajak1 = "UPDATE `transaksi` SET `nama_pajak_ori`= '".$result_final1[$key]."' where `kode_transaksi`= '".$nomor_transaksi."' and `kode_akun`='".$data_pajak1['akun_pajak_pembelian']."' and `nama_pajak_ori`='".$result_final1[$key]."'";
// 	    // mysqli_query($connect, $sql_pajak1);
// 	}
// 	foreach ($nama_pajak2 as $key => $value)  //pajak
// 	{
// 	    $arr = explode("|", $nama_pajak2[$key], 2);
// 	    $nama_pajak_saja = $arr[0];
// 	    $pajak_masukkan = "SELECT akun_pajak_pembelian from pajak where nama_pajak='".$nama_pajak_saja."'";
// 	    $hasil_query = mysqli_query($connect,$pajak_masukkan);
// 	    $data_pajak = mysqli_fetch_array($hasil_query);

// 	    $query_kode = "SELECT nama_pajak_ori from transaksi where kode_transaksi='".$nomor_transaksi."' and kode_akun='".$data_pajak['akun_pajak_pembelian']."' and nama_pajak_ori='".$nama_pajak2[$key]."'";
// 	    $hasil_query2 = mysqli_query($connect,$query_kode);
// 	    $nama_pajak = mysqli_fetch_array($hasil_query2);

// 	    $sql_pajak = "UPDATE `transaksi` SET `debit`= '".$uang_pajak2[$key]."' where `kode_transaksi`= '".$nomor_transaksi."' and `kode_akun`='".$data_pajak['akun_pajak_pembelian']."' and `nama_pajak_ori`='".$nama_pajak2[$key]."'";
// 	    mysqli_query($connect, $sql_pajak);
// 	}
	
	
// }
// elseif($jumlah2 > $jumlah)
// {
// 	foreach ($uang_pajak2 as $key => $value) 
// 	{
// 		if($nama_pajak2[$key] == $result_final2[$key])
// 		{
// 			echo $nama_pajak2[$key];
// 		}
// 	}
// }



	// $pajak_masukkan = "SELECT akun_pajak_pembelian from pajak where nama_pajak='".$nama_pajak_saja."'";
 //    $hasil_query = mysqli_query($connect,$pajak_masukkan);
 //    $data_pajak = mysqli_fetch_array($hasil_query);
	// $sql_penghapus_transaksi = "DELETE FROM `transaksi` where `kode_transaksi` = '".$nomor_transaksi."' )"; 
	// mysqli_query($connect, $sql_penghapus_transaksi);
// print_r($result);

if (count($nama_pajak3) == count($nama_pajak2))
{
	foreach ($nama_pajak3 as $key => $value)  //pajak
	{
	    $arr = explode("|", $nama_pajak2[$key], 2);
	    $nama_pajak_saja = $arr[0];
	    $pajak_masukkan = "SELECT akun_pajak_pembelian from pajak where nama_pajak='".$nama_pajak_saja."'";
	    $hasil_query = mysqli_query($connect,$pajak_masukkan);
	    $data_pajak = mysqli_fetch_array($hasil_query);
	    $query_kode = "SELECT nama_pajak_ori from transaksi where kode_transaksi='".$nomor_transaksi."' and kode_akun='".$data_pajak['akun_pajak_pembelian']."' and nama_pajak_ori='".$nama_pajak2[$key]."'";
	    $hasil_query2 = mysqli_query($connect,$query_kode);
	    $nama_pajak = mysqli_fetch_array($hasil_query2);
	    $sql_pajak = "UPDATE `transaksi` SET `debit`= '".$uang_pajak2[$key]."', `nama_pajak_ori`= '".$nama_pajak2[$key]."' where `kode_transaksi`= '".$nomor_transaksi."' and `kode_akun`='".$data_pajak['akun_pajak_pembelian']."' and `nama_pajak_ori`='".$nama_pajak3[$key]."'";
	    // mysqli_query($connect, $sql_pajak);
	}
}
elseif(count($nama_pajak3) != count($nama_pajak2))
{
	
	
}

// foreach ($nama_pajak3 as $key => $value)  //pajak
// {
//     $arr = explode("|", $nama_pajak2[$key], 2);
//     $nama_pajak_saja = $arr[0];
//     $pajak_masukkan = "SELECT akun_pajak_pembelian from pajak where nama_pajak='".$nama_pajak_saja."'";
//     $hasil_query = mysqli_query($connect,$pajak_masukkan);
//     $data_pajak = mysqli_fetch_array($hasil_query);
//     $query_kode = "SELECT nama_pajak_ori from transaksi where kode_transaksi='".$nomor_transaksi."' and kode_akun='".$data_pajak['akun_pajak_pembelian']."' and nama_pajak_ori='".$nama_pajak2[$key]."'";
//     $hasil_query2 = mysqli_query($connect,$query_kode);
//     $nama_pajak = mysqli_fetch_array($hasil_query2);
//     $sql_pajak = "UPDATE `transaksi` SET `debit`= '".$uang_pajak2[$key]."', `nama_pajak_ori`= '".$nama_pajak2[$key]."' where `kode_transaksi`= '".$nomor_transaksi."' and `kode_akun`='".$data_pajak['akun_pajak_pembelian']."' and `nama_pajak_ori`='".$nama_pajak3[$key]."'";
//     mysqli_query($connect, $sql_pajak);
// }


foreach ($akun_lama as $key => $value)  //akun
{
	$arr = explode("|", $nama_pajak_lama[$key], 2);
    $nama_pajak_saja = $arr[0];
    $pajak_masukkan = "SELECT akun_pajak_pembelian from pajak where nama_pajak='".$nama_pajak_saja."'";
    $hasil_query = mysqli_query($connect,$pajak_masukkan);
    $data_pajak = mysqli_fetch_array($hasil_query);

    $sql_kirim1 = "UPDATE `transaksi` set `kode_transaksi`= '".$nomor_transaksi."', `kode_akun`='".$akun_lama[$key]."', `debit`='".$uang_per_akun_lama[$key]."', harga_pajak='".$uang_pajak_perakun_lama[$key]."', nama_pajak='".$data_pajak['akun_pajak_pembelian']."',nama_pajak_ori='".$nama_pajak_lama[$key]."' where kode_akun='".$akun_patokan[$key]."' AND kode_transaksi='".$nomor_transaksi."'";
    // mysqli_query($connect, $sql_kirim1);
}


if(isset($_POST['uang_per_akun_baru']))
{
	$uang_per_akun_baru		= $_POST['uang_per_akun_baru'];
	$uang_pajak_perakun_baru= $_POST['uang_pajak_perakun_baru'];
	$nama_pajak_baru		= $_POST['nama_pajak_baru'];
	$deskripsi_baru			= $_POST['deskripsi_baru'];
	$akun_baru 				= $_POST['akun_baru'];
	foreach ($nama_pajak_baru as $key => $value) 
    {
        $arr = explode("|", $nama_pajak_baru[$key], 2);
        $nama_pajak_saja = $arr[0];
        $pajak_masukkan = "SELECT akun_pajak_pembelian from pajak where nama_pajak='".$nama_pajak_saja."'";
        $hasil_query = mysqli_query($connect,$pajak_masukkan);
        $data_pajak = mysqli_fetch_array($hasil_query);

        $sql_kirim_update = "INSERT INTO `transaksi`(`kode_transaksi`, `kode_akun`, `kolom`, `debit`, `no`, `nama_pajak`, `harga_pajak`, `nama_pajak_ori`) VALUES ('".$nomor_transaksi."', '".$akun_baru."', 'biaya', '".$uang_per_akun_baru[$key]."','".$noUrut."', '".$data_pajak['akun_pajak_pembelian']."', '".$uang_pajak_baru[$key]."',  '".$nama_pajak_baru[$key]."')";
        // mysqli_query($connect, $sql_kirim_update);
    } 
}



?>