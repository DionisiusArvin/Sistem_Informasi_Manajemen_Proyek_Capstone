Sistem Informasi Manajemen Proyek adalah sistem yang dibuat untuk memudahkan dalam memanajemen suatu proyek, khususnya proyek perencanaan dalam hal ini adalah bidang sipil dan aritektur.
1.	Kebutuhan Fungsional
Web harus mampu:
a.	Manajemen Tugas
    1)	Kepala divisi membuat task harian dari task induk.
    2)	Daily task memiliki atribut: nama, due date, deskripsi, status, assigned_to_staff_id, project_id.
b.	Penugasan Staff
    1)	Kepala divisi bisa mengubah penanggung jawab tugas (assigned_to_staff_id).
    2)	Staff bisa "claim" tugas jika belum ada penanggung jawab.
c.	Pelaporan Hasil Kerja
    1)	Staff dapat upload file (pdf, jpg, png, zip, dwg) & link hasil kerja.
    2)	Sistem mencatat aktivitas dalam TaskActivity (upload, revisi, validasi).
d.	Validasi Pekerjaan
    1)	Kepala divisi dapat approve (selesai) atau reject (revisi) pekerjaan.
    2)	Sistem mencatat status: Belum Dikerjakan, Menunggu Validasi, Revisi, Selesai.
e.	Monitoring
    1)	Progress pekerjaan otomatis (misal selesai → progress 100%).
    2)	History aktivitas tersimpan.
2.	Kebutuhan Non-Fungsional
a.	Keamanan:
    1)	Autentikasi role (kepala_divisi, staff).
    2)	Hanya user yang ditugaskan bisa upload file.
b.	Kinerja:
    1)	Aplikasi responsif, support mobile & desktop.
c.	Usability:
    1)	UI mudah dipahami, ada notifikasi sukses/error.
d.	Integritas Data:
    1)	Validasi input form, foreign key (task_id, project_id, user_id).
Front end :
Tailwind CSS
Back end :
PHP Laravel
