


<html>

<head>

<style type="text/css">
    body,div,table,thead,tbody,tfoot,tr,th,td,p { font-family:"Calibri"; font-size:x-small }
    a.comment-indicator:hover + comment { background:#ffd; position:absolute; display:block; border:1px solid black; padding:0.5em;  } 
    a.comment-indicator { background:red; display:inline-block; border:1px solid black; width:0.5em; height:0.5em;  } 
    comment { display:none;  } 
</style>

</head>

<body>
<table cellspacing="0" border="0">
<tr>
    <td style="width:5px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 2px solid #000000; border-right: 1px solid #000000" align="left" valign=middle ><b><font face="Arial" size=4 color="#000000">No</font></b></td>
    <td style="width:20px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="left" valign=middle ><b><font face="Arial" size=4 color="#000000">NAMA</font></b></td>
    <td style="width:20px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="left" valign=middle ><b><font face="Arial" size=4 color="#000000">Jabatan (BUMN Group maupun Jabatan Lain)</font></b></td>
    <td style="width:20px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="left" valign=middle ><b><font face="Arial" size=4 color="#000000">BUMN</font></b></td>
    <td style="width:20px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="left" valign=middle ><b><font face="Arial" size=4 color="#000000">Usia</font></b></td>
    
    @foreach($dynamic_filter as $a)
    <td style="width:20px; border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 2px solid #000000" align="left" valign=middle ><b><font face="Arial" size=4 color="#000000">{{$a->submenu}}</font></b></td>
    @endforeach

</tr>


<?php
    use App\Helpers\CVHelper;
?>

{{$num = 1 }}
@foreach($talenta as $a)
<tr>
    <td>{{$num++}}</td>
    <td>{{$a->nama_lengkap}}</td>
    <td>
        @if(empty($a->jabatan)) {{$a->riwayat_jabatan_lain}}
        @else  {{$a->jabatan}}
        @endif
    </td>
    <td>
        @if ($a->nama_perusahaan_talenta) {{ $a->nama_perusahaan_talenta }}
        @elseif ($a->nama_perusahaan) {{ $a->nama_perusahaan }}
        @else Tidak Ada Perusahaan
        @endif
    </td>
    <td>{{$a->usia}}</td>
    
    @foreach($dynamic_filter as $b)
    <td>{{ $a->{$b->alias} }}</td>
    @endforeach
</tr>
@endforeach

</table>
<!-- ************************************************************************** -->
</body>

</html>
