<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\IndikatorKinerjaKegiatan;
use App\Models\IndikatorKinerjaProgram;
use App\Models\ProgramStrategis;
use App\Models\SasaranKegiatan;
use Illuminate\Database\Seeder;
use App\Models\IKPColumn;
use App\Models\IKUPeriod;
use App\Models\IKUYear;

class IKUFormat2024Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = IKUYear::create([
            'year' => '2024'
        ]);

        for ($i = 1; $i <= 4; $i++) {
            IKUPeriod::create([
                'year_id' => $year->id,

                'period' => (string) $i,
                'status' => false,
            ]);
        }

        $format = [
            [
                'sk' => 'Meningkatnya Kualitas Lulusan Pendidikan Tinggi',
                'child' => [
                    [
                        'ikk' => 'Persentase lulusan S1 dan D4/D3/D2 yang berhasil mendapat pekerjaan; melanjutkan studi; atau menjadi wiraswasta.',
                        'child' => [
                            [
                                'ps' => 'Peningkatan Kualitas Lulusan yang Berdaya Saing',
                                'child' => [
                                    [
                                        'ikp' => 'Jumlah Lulusan yang mendapat pekerjaan',
                                        'definition' => 'Lulusan yang mendapat pekerjaan dalam rentang waktu 12 (dua belas) bulan terhitung mulai tanggal setelah terbit ijazah',
                                        'type' => 'iku',
                                        'column' => [
                                            'NIM',
                                            'Nama',
                                            'Nama PT',
                                            'Nama Prodi',
                                            'Tahun',
                                            'Status',
                                            'Bulan Tunggu',
                                            'Pendapatan',
                                            'Provinsi',
                                            'UMP',
                                            '1,2',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, Tracer Study/dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Jumlah Lulusan yang melanjutkan studi',
                                        'definition' => 'Lulusan yang melanjutkan studi pada jenjang S2/S2 terapan atau Profesi di dalam atau luar negeri dalam rentang waktu 12 (dua belas) bulan terhitung mulai tanggal setelah terbit ijazah',
                                        'type' => 'iku',
                                        'column' => [
                                            'NIM',
                                            'Nama Lulusan',
                                            'Nama PT Asal',
                                            'Jenjang Asal',
                                            'Nama Prodi Asal',
                                            'Tanggal Keluar',
                                            'Nama PT Lanjutan',
                                            'Jenjang Lanjutan',
                                            'Nama Prodi Lanjutan',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Jumlah Lulusan yang mendapatkan beasiswa lanjut studi',
                                        'definition' => 'Lulusan yang mendapatkan beasiswa studi lanjut dari lembaga/institusi dalam atau luar negeri dalam rentang waktu 12 (dua belas) bulan terhitung mulai tanggal setelah terbit ijazah',
                                        'type' => 'iku',
                                        'column' => [
                                            'NIM',
                                            'Nama Lulusan',
                                            'Nama PT Asal',
                                            'Jenjang Asal',
                                            'Nama Prodi Asal',
                                            'Tanggal Keluar',
                                            'Nama PT Lanjutan',
                                            'Jenjang Lanjutan',
                                            'Nama Prodi Lanjutan',
                                            'Beasiswa',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Jumlah Lulusan yang menjadi Wiraswasta',
                                        'definition' => 'Lulusan yang menjadi pendiri atau pasangan pendiri atau pekerja lepas dalam rentang waktu 12 (dua belas) bulan setelah lulus',
                                        'type' => 'iku',
                                        'column' => [
                                            'NIM',
                                            'Nama',
                                            'Nama PT',
                                            'Nama Prodi',
                                            'Tahun',
                                            'Status',
                                            'Bulan Tunggu',
                                            'Pendapatan',
                                            'Provinsi',
                                            'UMP',
                                            '1,2',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, Tracer Study/dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Persentase Lulusan yang mengisi data Tracer Study',
                                        'definition' => 'Lulusan T-1 yang mengisi data Tracer Study terhadap total lulusan T-1',
                                        'type' => 'iku',
                                        'column' => [
                                            'NIM',
                                            'Nama Lulusan',
                                            'Nama PT Asal',
                                            'Jenjang Asal',
                                            'Nama Prodi Asal',
                                            'Tanggal Keluar',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Jumlah Lulusan tepat waktu dengan masa studi 4 tahun',
                                        'definition' => 'Lulusan yang menempuh studi ≤ 4 tahun',
                                        'type' => 'ikt',
                                        'column' => [
                                            'NIM',
                                            'Nama Lulusan',
                                            'Nama PT Asal',
                                            'Jenjang Asal',
                                            'Nama Prodi Asal',
                                            'Tanggal Keluar',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Jumlah Lulusan dengan IPK ≥ 3,0',
                                        'definition' => 'Lulusan yang memiliki Indek Prestasi Kumulatif (IPK) ≥ 3,0',
                                        'type' => 'ikt',
                                        'column' => [
                                            'NIM',
                                            'Nama Lulusan',
                                            'Nama PT Asal',
                                            'Jenjang Asal',
                                            'Nama Prodi Asal',
                                            'Tanggal Keluar',
                                            'IPK',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Jumlah Lulusan berpredikat Pujian (Cumlaude)',
                                        'definition' => 'Lulusan yang memiliki IPK > 3,50; lulus tepat waktu 4 tahun; dan tidak pernah mengulang mata kuliah',
                                        'type' => 'ikt',
                                        'column' => [
                                            'NIM',
                                            'Nama Lulusan',
                                            'Nama PT Asal',
                                            'Jenjang Asal',
                                            'Nama Prodi Asal',
                                            'Tanggal Keluar',
                                            'IPK',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Jumlah Lulusan yang memiliki sertifikat kompetensi',
                                        'definition' => 'Lulusan yang memiliki sertifikat kompetensi sesuai dengan keahlian dalam cabang ilmunya dan/atau memiliki prestasi di luar program studinya',
                                        'type' => 'ikt',
                                        'column' => [
                                            'NIM',
                                            'Nama Lulusan',
                                            'Nama PT Asal',
                                            'Jenjang Asal',
                                            'Nama Prodi Asal',
                                            'Tanggal Keluar',
                                            'Sertifikat',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'sk' => 'Meningkatnya Kualitas Lulusan Pendidikan Tinggi',
                'child' => [
                    [
                        'ikk' => 'Persentase mahasiswa S1 dan D4/D3/D2 yang menjalankan kegiatan pembelajaran di luar program studi; atau meraih prestasi',
                        'child' => [
                            [
                                'ps' => 'Peningkatan Kualitas Mahasiswa yang Berdaya Saing',
                                'child' => [
                                    [
                                        'ikp' => 'Jumlah Mahasiswa yang mengikuti program Merdeka Belajar',
                                        'definition' => 'Mahasiswa yang menghabiskan minimal 10 SKS di luar program studi untuk melaksanakan kegiatan Merdeka Belajar',
                                        'type' => 'iku',
                                        'column' => [
                                            'NIM',
                                            'Nama Mahasiswa',
                                            'Nama PT',
                                            'Nama Prodi',
                                            'Jenjang',
                                            'Semester',
                                            'INTERNAL - Jumlah SKS Pertukaran Pelajar Internal',
                                            'EKSTERNAL - Jumlah SKS Non Pertukaran Pelajar Internal',
                                            'EKSTERNAL - Jumlah SKS Pertukaran Pelajaran Eksternal',
                                            'EKSTERNAL - Jumlah SKS MBKM Flagship',
                                            'EKSTERNAL - Jumlah SKS Reguler',
                                            'EKSTERNAL - Jumlah SKS Reguler',
                                            'Total SKS Ditempuh',
                                            'SKS Valid',
                                            'Total SKS MBKM',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Jumlah Mahasiswa yang mendapat penghargaan di tingkat Provinsi',
                                        'definition' => 'Mahasiswa yang mendapatkan peringkat I-III dalam kompetisi akademik/non akademik di tingkat provinsi',
                                        'type' => 'iku',
                                        'column' => [
                                            'NIM',
                                            'Nama Mahasiswa',
                                            'Nama Prodi',
                                            'Tanggal',
                                            'Nama Prestasi',
                                            'Level',
                                            'Peringkat',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Jumlah Mahasiswa yang mendapat penghargaan di tingkat Nasional',
                                        'definition' => 'Mahasiswa yang mendapatkan peringkat I-III dalam kompetisi akademik/non akademik di tingkat Nasional',
                                        'type' => 'iku',
                                        'column' => [
                                            'NIM',
                                            'Nama Mahasiswa',
                                            'Nama Prodi',
                                            'Tanggal',
                                            'Nama Prestasi',
                                            'Level',
                                            'Peringkat',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Jumlah Mahasiswa yang mendapat penghargaan di tingkat Internasional',
                                        'definition' => 'Mahasiswa yang mendapatkan peringkat I-III dalam kompetisi akademik/non akademik di tingkat Internasional',
                                        'type' => 'iku',
                                        'column' => [
                                            'NIM',
                                            'Nama Mahasiswa',
                                            'Nama Prodi',
                                            'Tanggal',
                                            'Nama Prestasi',
                                            'Level',
                                            'Peringkat',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Jumlah Mahasiswa yang memiliki sertifikasi kompetensi internasional',
                                        'definition' => 'Mahasiswa yang memiliki sertifikat kompetensi sesuai dengan keahlian dalam cabang ilmunya dan/atau memiliki prestasi di luar program studinya',
                                        'type' => 'iku',
                                        'column' => [
                                            'NIM',
                                            'Nama Mahasiswa',
                                            'Nama Prodi',
                                            'Tahun',
                                            'Nama Sertifikasi',
                                            'Nama Pemberi Sertifikasi',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Jumlah Mahasiswa Inbound yang diterima dalam pertukaran mahasiswa',
                                        'definition' => 'Mahasiswa di luar ITERA yang melakukan pertukaran di dalam program studi yang berada di lingkungan ITERA',
                                        'type' => 'iku',
                                        'column' => [
                                            'NIM',
                                            'Nama Mahasiswa',
                                            'Nama PT',
                                            'Nama Prodi',
                                            'Jenjang',
                                            'Semester',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'sk' => 'Meningkatnya kualitas dosen pendidikan tinggi',
                'child' => [
                    [
                        'ikk' => 'Persentase dosen yang berkegiatan tridarma di perguruan tinggi lain, bekerja sebagai praktisi di dunia industri, atau membimbing mahasiswa berkegiatan di luar program studi.',
                        'child' => [
                            [
                                'ps' => 'Peningkatan Kualitas Dosen yang Unggul dan Profesional',
                                'child' => [
                                    [
                                        'ikp' => 'Jumlah dosen ITERA yang berkegiatan tri dharma di kampus lain di QS 100 berdasarkan bidang ilmu atau PT dalam negeri lainnya',
                                        'definition' => 'Dosen yang berkegiatan tri dharma di kampus lain di QS 100 berdasarkan bidang ilmu atau PT dalam negeri lainnya dalam kurun waktu 5 (lima) tahun terakhir',
                                        'type' => 'iku',
                                        'column' => [
                                            'Nama Dosen',
                                            'Prodi',
                                            'Nama PT Kampus Lain',
                                            'Nama Prodi Kampus Lain',
                                            'Mata Kuliah',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Jumlah dosen ITERA yang menjadi praktisi di dunia industri',
                                        'definition' => 'Dosen ITERA yang bekerja sebagai peneliti, konsultan, asesor, pegawai penuh waktu (full time), atau paruh waktu (part time), wiraswasta pendiri (founder) atau wiraswasta pasangan pendiri (co-founder) di dunia Industri dalam kurun waktu 5 (lima) tahun terakhir',
                                        'type' => 'iku',
                                        'column' => [
                                            'Nama Dosen',
                                            'Prodi',
                                            'Jenis Kegiatan',
                                            'Judul',
                                            'Bidang Keilmuan',
                                            'Tahun Pelaksanaan',
                                            'Lama Kegiatan',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Jumlah dosen ITERA yang membimbing mahasiswa berkegiatan di luar program studi',
                                        'definition' => 'Dosen ITERA yang membimbing mahasiswa berkegiatan di luar program studi dibuktikan dengan surat tugas/surat keputusan dari program studi atau fakultas',
                                        'type' => 'iku',
                                        'column' => [
                                            'Nama Dosen',
                                            'Prodi',
                                            'Jenis Aktifitas',
                                            'Judul Aktifitas',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Jumlah dosen ITERA menjadi keynote speaker/narasumber dalam seminar atau workshop berskala nasional/internasional',
                                        'definition' => 'Dosen ITERA menjadi keynote speaker/narasumber dalam seminar atau workshop berskala nasional/internasional',
                                        'type' => 'iku',
                                        'column' => [
                                            'Nama Dosen',
                                            'Prodi',
                                            'Nama Pekerjaan',
                                            'Instansi',
                                            'Bidang',
                                            'Nama Jabatan',
                                            'Divisi',
                                            'Waktu Mulai',
                                            'Waktu Akhir',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'sk' => 'Meningkatnya kualitas dosen pendidikan tinggi',
                'child' => [
                    [
                        'ikk' => 'Persentase dosen yang memiliki sertifikat kompetensi/profesi yangn diakui oleh dunia usaha dan dunia industri; atau persentase pengajar yang berasal dari kalangan praktisi profesional, dunia usaha, atau dunia industri.',
                        'child' => [
                            [
                                'ps' => 'Peningkatan Kualitas Dosen yang Unggul dan Profesional',
                                'child' => [
                                    [
                                        'ikp' => 'Jumlah dosen ITERA yang memiliki sertifikat kompetensi atau profesi',
                                        'definition' => 'Dosen ITERA yang memiliki sertifikat kompetensi/profesi sesuai dengan keahlian dalam cabang ilmunya dan/atau di luar bidang keilmuannya dan diakui oleh dunia usaha dan dunia industri
                                        
                                        daftar situs untuk sertifikasi yang diakui:
                                        https://bnsp.go.id/lsp
                                        https://fortune.com/ranking/fortune500
                                        
                                        Sertifikasi profesi dosen tidak termasuk dalam penilaian indikator ini.',
                                        'type' => 'iku',
                                        'column' => [
                                            'Nama Dosen',
                                            'Nama Prodi',
                                            'Jenis Sertifikasi',
                                            'Bidang Studi',
                                            'Nomor',
                                            'Tahun',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'ps' => 'Praktisi Mengajar',
                                'child' => [
                                    [
                                        'ikp' => 'Jumlah pengajar yang berasal dari kalangan praktisi profesional, dunia usaha, atau dunia industri.',
                                        'definition' => 'Pengajar yang berasal dari kalangan praktisi profesional, dunia usaha, atau dunia industri.',
                                        'type' => 'iku',
                                        'column' => [
                                            'PDDIKTI - Nama Dosen',
                                            'PDDIKTI - NIDN',
                                            'FLAGSHIP - Nama Praktisi',
                                            'FLAGSHIP - Instansi Asal',
                                            'Nama Prodi',
                                            'PDDIKTI - Mata Kuliah',
                                            'FLAGSHIP - Jam Mengajar',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'sk' => 'Meningkatnya kualitas kurikulum dan pembelajaran',
                'child' => [
                    [
                        'ikk' => 'Jumlah kerjasama per program studi S1 dan D4/D3/D2/D1',
                        'child' => [
                            [
                                'ps' => 'Peningkatan jumlah kerjasama yang dilakukan oleh program studi',
                                'child' => [
                                    [
                                        'ikp' => 'Jumlah kerjasama Program Studi dengan mitra untuk peningkatan kualitas mahasiswa melalui kegiatan pembelajaran di luar Program Studi',
                                        'definition' => 'Kerjasama Program Studi dengan mitra untuk peningkatan kualitas mahasiswa melalui kegiatan pembelajaran di luar Program Studi',
                                        'type' => 'iku',
                                        'column' => [
                                            'Prodi',
                                            'Nama Mitra',
                                            'Kelas Mitra',
                                            'Bentuk Kegiatan',
                                            'Klasifikasi Mitra',
                                            'Jenis Dokumen Kerjasama',
                                            'Tanggal Mulai',
                                            'Tanggal',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Jumlah kerjasama penelitian dengan mitra perguruan tinggi dalam negeri dan luar negeri, lembaga penelitian, industry/perusahaan/instansi pemerintah',
                                        'definition' => 'Kerjasama di bidang penelitian dengan mitra perguruan tinggi dalam negeri dan luar negeri, lembaga penelitian, industry/perusahaan/instansi pemerintah',
                                        'type' => 'iku',
                                        'column' => [
                                            'Prodi',
                                            'Nama Mitra',
                                            'Kelas Mitra',
                                            'Bentuk Kegiatan',
                                            'Klasifikasi Mitra',
                                            'Jenis Dokumen Kerjasama',
                                            'Tanggal Mulai',
                                            'Tanggal',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Jumlah kerjasama pengabdian masyarakat dengan mitra',
                                        'definition' => 'Kerjasama di bidang pengabdian masyarakat dengan mitra',
                                        'type' => 'iku',
                                        'column' => [
                                            'Prodi',
                                            'Nama Mitra',
                                            'Kelas Mitra',
                                            'Bentuk Kegiatan',
                                            'Klasifikasi Mitra',
                                            'Jenis Dokumen Kerjasama',
                                            'Tanggal Mulai',
                                            'Tanggal',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'sk' => 'Meningkatnya kualitas kurikulum dan pembelajaran',
                'child' => [
                    [
                        'ikk' => 'Persentase mata kuliah S1 dan D4/D3/D2/D1 yang menggunakan metode pembelajaran pemecahan kasus (case method) atau pembelajaran kelompok berbasis projek (team-based project) sebagai sebagian bobot evaluasi',
                        'child' => [
                            [
                                'ps' => 'Peningkatan kualitas mata kuliah',
                                'child' => [
                                    [
                                        'ikp' => 'Jumlah mata kuliah yang menggunakan metode pembelajaran pemecah kasus (case method)',
                                        'definition' => 'Mata Kuliah yang menggunakan metode pembelajaran pemecahan kasus (case method)',
                                        'type' => 'iku',
                                        'column' => [
                                            'Nama PT',
                                            'Nama Prodi',
                                            'Jenjang',
                                            'Semester',
                                            'Kode Mata Kuliah',
                                            'Nama Mata Kuliah',
                                            'Nama Kelas',
                                            'SKS',
                                            'Metode',
                                            'Bobot CBL/PBL',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Jumlah mata kuliah yang menggunakan metode pembelajaran pembelajaran kelompok berbasis project (team-based project)',
                                        'definition' => 'Mata Kuliah yang menggunakan metode pembelajaran kelompok berbasis project (team-based project)',
                                        'type' => 'iku',
                                        'column' => [
                                            'Nama PT',
                                            'Nama Prodi',
                                            'Jenjang',
                                            'Semester',
                                            'Kode Mata Kuliah',
                                            'Nama Mata Kuliah',
                                            'Nama Kelas',
                                            'SKS',
                                            'Metode',
                                            'Bobot CBL/PBL',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                    [
                                        'ikp' => 'Jumlah dosen yang mengikuti pelatihan pengembangan pembelajaran',
                                        'definition' => 'Dosen yang mengikuti pelatihan pengembangan pembelajaran',
                                        'type' => 'ikt',
                                        'column' => [
                                            'Nama PT',
                                            'Nama Prodi',
                                            'Jenjang',
                                            'Semester',
                                            'Kode Mata Kuliah',
                                            'Nama Mata Kuliah',
                                            'Nama Kelas',
                                            'SKS',
                                            'Metode',
                                            'Bobot CBL/PBL',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'sk' => 'Meningkatnya kualitas kurikulum dan pembelajaran',
                'child' => [
                    [
                        'ikk' => 'Persentase program studi S1 dan D4/D3 yang memiliki akreditasi atau sertifikat internasional yang diakui pemerintah',
                        'child' => [
                            [
                                'ps' => 'Peningkatan akreditasi program studi',
                                'child' => [
                                    [
                                        'ikp' => 'Jumlah program studi terakreditasi nasional dengan predikat unggul',
                                        'definition' => 'Program studi yang memiliki akreditasi nasional dengan predikat unggul',
                                        'type' => 'ikt',
                                        'column' => [
                                            'Nama Prodi',
                                            'Akreditasi',
                                            'LINK - Bukti SS sudah Upload Sistem (Siakad/Sister, dll)',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'sk' => 'Meningkatnya tata kelola satuan kerja di lingkungan Ditjen Pendidikan Tinggi',
                'child' => [
                    [
                        'ikk' => 'Nilai Kinerja Anggaran atas Pelaksanaan RKA-K/L',
                        'child' => [
                            [
                                'ps' => 'Peningkatan Kinerja Anggaran Institusi',
                                'child' => [
                                    [
                                        'ikp' => 'Persentase Penyerapan Anggaran',
                                        'definition' => 'Persentase Penyerapan Anggaran pada tahun berjalan',
                                        'mode' => 'single',
                                        'type' => 'ikt',
                                    ],
                                ],
                            ],
                            [
                                'ps' => 'Penguatan Akuntabilitas',
                                'child' => [
                                    [
                                        'ikp' => 'Tersedianya dokumen Laporan Capaian Kinerja Unit',
                                        'definition' => 'Tersedianya dokume Laporan Capaian Kinerja Unit pada tahun berjalan',
                                        'type' => 'ikt',
                                        'column' => [
                                            'Tahun',
                                            'Dokumen',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'ps' => 'Peningkatan Kualitas Layanan Publik',
                                'child' => [
                                    [
                                        'ikp' => 'Indeks Kepuasan Masyarakat terhadap pelayanan publik',
                                        'definition' => 'Indeks Kepuasan Masyarakat terhadap pelayanan publik',
                                        'mode' => 'single',
                                        'type' => 'ikt',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($format as $skKey => $sk) {
            $skData = SasaranKegiatan::create([
                'time_id' => $year->id,

                'number' => $skKey + 1,
                'name' => $sk['sk'],
            ]);
            foreach ($sk['child'] as $ikkKey => $ikk) {
                $ikkData = IndikatorKinerjaKegiatan::create([
                    'sasaran_kegiatan_id' => $skData->id,

                    'number' => $ikkKey + 1,
                    'name' => $ikk['ikk'],
                ]);
                foreach ($ikk['child'] as $psKey => $ps) {
                    $psData = ProgramStrategis::create([
                        'indikator_kinerja_kegiatan_id' => $ikkData->id,

                        'number' => $psKey + 1,
                        'name' => $ps['ps'],
                    ]);
                    foreach ($ps['child'] as $ikpKey => $ikp) {
                        $ikpData = IndikatorKinerjaProgram::create([
                            'program_strategis_id' => $psData->id,

                            'number' => $ikpKey + 1,
                            'definition' => $ikp['definition'],
                            'mode' => $ikp['mode'] ?? 'table',
                            'type' => $ikp['type'],
                            'name' => $ikp['ikp'],
                            'status' => 'aktif',
                        ]);

                        if (!isset($ikp['mode'])) {
                            foreach ($ikp['column'] as $colKey => $col) {
                                $file = false;
                                if (isset($ikp['column'][$colKey + 1])) {
                                    if ($ikp['column'][$colKey + 1] === 1) {
                                        $file = true;
                                    }
                                }
                                if ($col !== 1) {
                                    IKPColumn::create([
                                        'indikator_kinerja_program_id' => $ikpData->id,

                                        'number' => $colKey + 1,
                                        'file' => $file,
                                        'name' => $col,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
