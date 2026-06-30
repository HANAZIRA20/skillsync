<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Client;
use App\Models\Project;
use App\Models\StudentSkill;
use App\Models\Matching;
use App\Models\Payment;
use App\Models\Workroom;
use App\Models\Revision;
use App\Models\Portfolio;
use App\Models\FactProjectActivity;
use App\Models\AnalyticsEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // 1. ADMIN
        // ============================================================
        $admin = User::create([
            'name'     => 'Admin SkillSync',
            'email'    => 'admin@skillsync.id',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'is_active'=> true,
        ]);

        // ============================================================
        // 2. MAHASISWA (5 users)
        // ============================================================
        $mahasiswaData = [
            ['name'=>'Budi Santoso',   'email'=>'budi@student.id',    'universitas'=>'Universitas Indonesia',    'jurusan'=>'Teknik Informatika',  'nim'=>'2021001', 'ipk'=>3.75, 'semester'=>6, 'bio'=>'Full-stack developer dengan passion di AI dan web development.'],
            ['name'=>'Siti Rahayu',    'email'=>'siti@student.id',    'universitas'=>'Institut Teknologi Bandung','jurusan'=>'Sistem Informasi',     'nim'=>'2021002', 'ipk'=>3.90, 'semester'=>7, 'bio'=>'UI/UX designer & frontend developer berpengalaman.'],
            ['name'=>'Ahmad Fauzi',    'email'=>'ahmad@student.id',   'universitas'=>'Universitas Gadjah Mada',  'jurusan'=>'Ilmu Komputer',        'nim'=>'2021003', 'ipk'=>3.60, 'semester'=>5, 'bio'=>'Data scientist yang gemar menganalisis data bisnis.'],
            ['name'=>'Dewi Lestari',   'email'=>'dewi@student.id',    'universitas'=>'Universitas Brawijaya',    'jurusan'=>'Teknik Informatika',  'nim'=>'2021004', 'ipk'=>3.80, 'semester'=>8, 'bio'=>'Mobile developer spesialis Android & Flutter.'],
            ['name'=>'Rizki Pratama',  'email'=>'rizki@student.id',   'universitas'=>'Universitas Diponegoro',   'jurusan'=>'Teknik Elektro',       'nim'=>'2021005', 'ipk'=>3.55, 'semester'=>6, 'bio'=>'IoT enthusiast dengan keahlian embedded systems.'],
        ];

        $students = [];
        foreach ($mahasiswaData as $mData) {
            $user = User::create([
                'name'      => $mData['name'],
                'email'     => $mData['email'],
                'password'  => Hash::make('password'),
                'role'      => 'mahasiswa',
                'is_active' => true,
            ]);
            $student = Student::create([
                'user_id'       => $user->id,
                'nim'           => $mData['nim'],
                'universitas'   => $mData['universitas'],
                'jurusan'       => $mData['jurusan'],
                'ipk'           => $mData['ipk'],
                'semester'      => $mData['semester'],
                'bio'           => $mData['bio'],
                'krs_status'    => 'parsed',
                'total_projects'=> 0,
                'total_earnings'=> 0,
                'average_rating'=> 0,
            ]);
            $students[] = $student;
        }

        // Skills untuk tiap mahasiswa
        $skillSets = [
            ['Laravel', 'Vue.js', 'PHP', 'MySQL', 'REST API', 'Tailwind CSS'],
            ['Figma', 'React', 'CSS3', 'UI/UX Design', 'Adobe XD', 'JavaScript'],
            ['Python', 'Machine Learning', 'Pandas', 'TensorFlow', 'Data Analysis', 'SQL'],
            ['Flutter', 'Dart', 'Android', 'Firebase', 'Kotlin', 'REST API'],
            ['Arduino', 'IoT', 'Python', 'C++', 'Raspberry Pi', 'MQTT'],
        ];

        foreach ($students as $i => $student) {
            foreach ($skillSets[$i] as $j => $skill) {
                StudentSkill::create([
                    'student_id'       => $student->id,
                    'skill_name'       => $skill,
                    'category'         => $j < 2 ? 'programming' : 'framework',
                    'confidence_score' => rand(70, 98) / 100,
                    'source'           => 'ai_parsed',
                ]);
            }
        }

        // ============================================================
        // 3. CLIENTS (3 UMKM)
        // ============================================================
        $clientData = [
            ['name'=>'Pak Andi Wijaya',   'email'=>'andi@tokobaju.id',   'company'=>'Toko Baju Modern',     'industry'=>'E-Commerce',    'city'=>'Jakarta'],
            ['name'=>'Bu Sari Kusuma',    'email'=>'sari@warungdigital.id','company'=>'Warung Digital Nusantara','industry'=>'F&B Tech',  'city'=>'Bandung'],
            ['name'=>'Pak Doni Setiawan', 'email'=>'doni@konsultanit.id', 'company'=>'Konsultan IT Mandiri', 'industry'=>'IT Services',   'city'=>'Surabaya'],
        ];

        $clients = [];
        foreach ($clientData as $cData) {
            $user = User::create([
                'name'      => $cData['name'],
                'email'     => $cData['email'],
                'password'  => Hash::make('password'),
                'role'      => 'client',
                'is_active' => true,
            ]);
            $client = Client::create([
                'user_id'                 => $user->id,
                'company_name'            => $cData['company'],
                'industry'                => $cData['industry'],
                'city'                    => $cData['city'],
                'total_projects_posted'   => 0,
                'total_projects_completed'=> 0,
                'total_spent'             => 0,
            ]);
            $clients[] = $client;
        }

        // ============================================================
        // 4. PROJECTS (10 proyek dengan berbagai status)
        // ============================================================
        $projectsData = [
            // --- Client 0 (Toko Baju) ---
            [
                'client_id'       => $clients[0]->id,
                'title'           => 'Pembuatan Website E-Commerce Baju Modern',
                'description'     => 'Kami membutuhkan website e-commerce lengkap dengan fitur keranjang belanja, payment gateway, dan manajemen produk. Website harus responsive dan SEO-friendly. Menggunakan Laravel + Vue.js dengan desain modern.',
                'category'        => 'Web Development',
                'required_skills' => ['Laravel', 'Vue.js', 'MySQL', 'Tailwind CSS'],
                'budget_min'      => 1500000,
                'budget_max'      => 3000000,
                'deadline'        => now()->addDays(30),
                'duration_days'   => 30,
                'status'          => 'completed',
                'selected_student_idx' => 0,
                'agreed_budget'   => 2500000,
                'max_revisions'   => 3,
                'revision_count'  => 1,
            ],
            [
                'client_id'       => $clients[0]->id,
                'title'           => 'Desain UI/UX Aplikasi Mobile Toko Baju',
                'description'     => 'Diperlukan desain UI/UX yang modern dan user-friendly untuk aplikasi mobile toko baju kami. Desain harus mencakup onboarding, katalog produk, checkout, dan tracking pesanan.',
                'category'        => 'UI/UX Design',
                'required_skills' => ['Figma', 'UI/UX Design', 'Adobe XD'],
                'budget_min'      => 800000,
                'budget_max'      => 1500000,
                'deadline'        => now()->addDays(20),
                'duration_days'   => 20,
                'status'          => 'in_progress',
                'selected_student_idx' => 1,
                'agreed_budget'   => 1200000,
                'max_revisions'   => 2,
                'revision_count'  => 0,
            ],
            [
                'client_id'       => $clients[0]->id,
                'title'           => 'Analisis Data Penjualan dan Dashboard Laporan',
                'description'     => 'Kami membutuhkan analisis mendalam dari data penjualan 2 tahun terakhir dan pembuatan dashboard interaktif untuk monitoring performa bisnis secara real-time.',
                'category'        => 'Data Science',
                'required_skills' => ['Python', 'Data Analysis', 'Pandas', 'SQL'],
                'budget_min'      => 1000000,
                'budget_max'      => 2000000,
                'deadline'        => now()->addDays(45),
                'duration_days'   => 45,
                'status'          => 'open',
                'selected_student_idx' => null,
                'agreed_budget'   => null,
                'max_revisions'   => 2,
                'revision_count'  => 0,
            ],
            // --- Client 1 (Warung Digital) ---
            [
                'client_id'       => $clients[1]->id,
                'title'           => 'Aplikasi Kasir dan Manajemen Stok Warung',
                'description'     => 'Butuh aplikasi kasir sederhana berbasis mobile untuk warung makan kami. Fitur: input transaksi, manajemen menu, laporan harian, dan notifikasi stok habis.',
                'category'        => 'Mobile Development',
                'required_skills' => ['Flutter', 'Dart', 'Firebase'],
                'budget_min'      => 1200000,
                'budget_max'      => 2500000,
                'deadline'        => now()->addDays(25),
                'duration_days'   => 25,
                'status'          => 'in_review',
                'selected_student_idx' => 3,
                'agreed_budget'   => 2000000,
                'max_revisions'   => 3,
                'revision_count'  => 2,
            ],
            [
                'client_id'       => $clients[1]->id,
                'title'           => 'Sistem IoT Monitoring Suhu dan Kelembaban Gudang',
                'description'     => 'Sistem monitoring berbasis IoT untuk memantau suhu dan kelembaban gudang penyimpanan bahan makanan secara real-time dengan notifikasi otomatis ke HP.',
                'category'        => 'IoT / Embedded',
                'required_skills' => ['Arduino', 'IoT', 'Python', 'MQTT'],
                'budget_min'      => 800000,
                'budget_max'      => 1800000,
                'deadline'        => now()->addDays(35),
                'duration_days'   => 35,
                'status'          => 'open',
                'selected_student_idx' => null,
                'agreed_budget'   => null,
                'max_revisions'   => 2,
                'revision_count'  => 0,
            ],
            [
                'client_id'       => $clients[1]->id,
                'title'           => 'Website Profil dan Menu Online Warung Digital',
                'description'     => 'Website landing page yang menarik untuk memperkenalkan warung digital kami, menampilkan menu, harga, lokasi, dan form pemesanan online yang mudah digunakan.',
                'category'        => 'Web Development',
                'required_skills' => ['Laravel', 'PHP', 'MySQL', 'Tailwind CSS'],
                'budget_min'      => 500000,
                'budget_max'      => 1000000,
                'deadline'        => now()->addDays(15),
                'duration_days'   => 15,
                'status'          => 'waiting_payment',
                'selected_student_idx' => 0,
                'agreed_budget'   => 800000,
                'max_revisions'   => 2,
                'revision_count'  => 0,
            ],
            // --- Client 2 (Konsultan IT) ---
            [
                'client_id'       => $clients[2]->id,
                'title'           => 'Pengembangan REST API untuk Sistem HR',
                'description'     => 'Dibutuhkan pengembangan REST API yang robust untuk sistem HR internal perusahaan. Mencakup manajemen karyawan, absensi, penggajian, dan laporan. Dokumentasi API wajib disertakan.',
                'category'        => 'Backend Development',
                'required_skills' => ['Laravel', 'REST API', 'MySQL', 'PHP'],
                'budget_min'      => 2000000,
                'budget_max'      => 4000000,
                'deadline'        => now()->addDays(40),
                'duration_days'   => 40,
                'status'          => 'revision',
                'selected_student_idx' => 0,
                'agreed_budget'   => 3500000,
                'max_revisions'   => 3,
                'revision_count'  => 1,
            ],
            [
                'client_id'       => $clients[2]->id,
                'title'           => 'Machine Learning Model untuk Prediksi Churn Customer',
                'description'     => 'Butuh model ML untuk memprediksi customer yang akan berhenti berlangganan layanan kami. Data sudah tersedia dalam format CSV. Diperlukan model dengan akurasi minimal 80%.',
                'category'        => 'Machine Learning',
                'required_skills' => ['Python', 'Machine Learning', 'TensorFlow', 'Pandas'],
                'budget_min'      => 1500000,
                'budget_max'      => 3000000,
                'deadline'        => now()->addDays(60),
                'duration_days'   => 60,
                'status'          => 'open',
                'selected_student_idx' => null,
                'agreed_budget'   => null,
                'max_revisions'   => 2,
                'revision_count'  => 0,
            ],
            [
                'client_id'       => $clients[2]->id,
                'title'           => 'Dashboard Analytics Real-Time untuk Monitoring KPI',
                'description'     => 'Pembuatan dashboard analytics interaktif untuk monitoring KPI bisnis secara real-time. Integrasi dengan database PostgreSQL yang sudah ada. Harus bisa di-embed di intranet perusahaan.',
                'category'        => 'Data Science',
                'required_skills' => ['Python', 'Data Analysis', 'SQL', 'Pandas'],
                'budget_min'      => 1200000,
                'budget_max'      => 2500000,
                'deadline'        => now()->addDays(50),
                'duration_days'   => 50,
                'status'          => 'open',
                'selected_student_idx' => null,
                'agreed_budget'   => null,
                'max_revisions'   => 3,
                'revision_count'  => 0,
            ],
            [
                'client_id'       => $clients[2]->id,
                'title'           => 'Chatbot Customer Service Berbasis AI',
                'description'     => 'Pengembangan chatbot AI untuk customer service perusahaan yang mampu menjawab FAQ, memproses tiket keluhan, dan mengintegrasikan dengan sistem CRM yang sudah ada.',
                'category'        => 'AI / Chatbot',
                'required_skills' => ['Python', 'Machine Learning', 'REST API', 'Laravel'],
                'budget_min'      => 2000000,
                'budget_max'      => 4500000,
                'deadline'        => now()->addDays(55),
                'duration_days'   => 55,
                'status'          => 'completed',
                'selected_student_idx' => 2,
                'agreed_budget'   => 4000000,
                'max_revisions'   => 3,
                'revision_count'  => 0,
            ],
        ];

        $projects = [];
        foreach ($projectsData as $pData) {
            $selectedStudentId = isset($pData['selected_student_idx']) && $pData['selected_student_idx'] !== null
                ? $students[$pData['selected_student_idx']]->id
                : null;

            $project = Project::create([
                'client_id'          => $pData['client_id'],
                'title'              => $pData['title'],
                'description'        => $pData['description'],
                'category'           => $pData['category'],
                'required_skills'    => $pData['required_skills'],
                'budget_min'         => $pData['budget_min'],
                'budget_max'         => $pData['budget_max'],
                'agreed_budget'      => $pData['agreed_budget'],
                'deadline'           => $pData['deadline'],
                'duration_days'      => $pData['duration_days'],
                'status'             => $pData['status'],
                'selected_student_id'=> $selectedStudentId,
                'max_revisions'      => $pData['max_revisions'],
                'revision_count'     => $pData['revision_count'],
            ]);
            $projects[] = $project;

            // Record fact for project creation
            FactProjectActivity::record([
                'project_id'       => $project->id,
                'client_id'        => $pData['client_id'],
                'student_id'       => $selectedStudentId,
                'activity_type'    => 'project_created',
                'activity_category'=> 'client',
                'project_title'    => $project->title,
                'project_category' => $project->category,
                'amount'           => $project->budget_max,
                'skills_involved'  => $project->required_skills,
                'project_status'   => $project->status,
            ]);
        }

        // Update client stats
        $clients[0]->update(['total_projects_posted' => 3, 'total_projects_completed' => 1, 'total_spent' => 2500000]);
        $clients[1]->update(['total_projects_posted' => 3, 'total_projects_completed' => 0, 'total_spent' => 2000000]);
        $clients[2]->update(['total_projects_posted' => 4, 'total_projects_completed' => 1, 'total_spent' => 4000000]);

        // ============================================================
        // 5. AI MATCHINGS
        // ============================================================
        // For open projects, create matchings with all relevant students
        $matchingData = [
            // Project 2 (Data Science - open) - match Ahmad & Budi
            ['project_idx'=>2, 'student_idx'=>2, 'score'=>0.92, 'matched_skills'=>['Python','Data Analysis','Pandas','SQL'], 'missing_skills'=>[]],
            ['project_idx'=>2, 'student_idx'=>0, 'score'=>0.71, 'matched_skills'=>['SQL'], 'missing_skills'=>['Python','Pandas']],
            // Project 4 (IoT - open) - match Rizki
            ['project_idx'=>4, 'student_idx'=>4, 'score'=>0.95, 'matched_skills'=>['Arduino','IoT','Python','MQTT'], 'missing_skills'=>[]],
            ['project_idx'=>4, 'student_idx'=>2, 'score'=>0.65, 'matched_skills'=>['Python'], 'missing_skills'=>['Arduino','IoT','MQTT']],
            // Project 7 (ML - open) - match Ahmad
            ['project_idx'=>7, 'student_idx'=>2, 'score'=>0.96, 'matched_skills'=>['Python','Machine Learning','TensorFlow','Pandas'], 'missing_skills'=>[]],
            ['project_idx'=>7, 'student_idx'=>0, 'score'=>0.58, 'matched_skills'=>['PHP','Laravel'], 'missing_skills'=>['TensorFlow','ML']],
            // Project 8 (Analytics - open) - match Ahmad & Dewi
            ['project_idx'=>8, 'student_idx'=>2, 'score'=>0.89, 'matched_skills'=>['Python','Data Analysis','SQL','Pandas'], 'missing_skills'=>[]],
            ['project_idx'=>8, 'student_idx'=>3, 'score'=>0.62, 'matched_skills'=>['REST API','Firebase'], 'missing_skills'=>['Python','Pandas']],
            // Completed/in-progress projects matching (already selected)
            ['project_idx'=>0, 'student_idx'=>0, 'score'=>0.94, 'status'=>'selected', 'matched_skills'=>['Laravel','Vue.js','MySQL','Tailwind CSS'], 'missing_skills'=>[]],
            ['project_idx'=>1, 'student_idx'=>1, 'score'=>0.96, 'status'=>'selected', 'matched_skills'=>['Figma','UI/UX Design','Adobe XD'], 'missing_skills'=>[]],
            ['project_idx'=>3, 'student_idx'=>3, 'score'=>0.93, 'status'=>'selected', 'matched_skills'=>['Flutter','Dart','Firebase'], 'missing_skills'=>[]],
            ['project_idx'=>5, 'student_idx'=>0, 'score'=>0.87, 'status'=>'selected', 'matched_skills'=>['Laravel','PHP','MySQL','Tailwind CSS'], 'missing_skills'=>[]],
            ['project_idx'=>6, 'student_idx'=>0, 'score'=>0.91, 'status'=>'selected', 'matched_skills'=>['Laravel','REST API','MySQL','PHP'], 'missing_skills'=>[]],
            ['project_idx'=>9, 'student_idx'=>2, 'score'=>0.88, 'status'=>'selected', 'matched_skills'=>['Python','Machine Learning','REST API'], 'missing_skills'=>['Laravel']],
        ];

        $matchings = [];
        foreach ($matchingData as $mData) {
            $matching = Matching::create([
                'project_id'    => $projects[$mData['project_idx']]->id,
                'student_id'    => $students[$mData['student_idx']]->id,
                'match_score'   => $mData['score'],
                'matched_skills'=> $mData['matched_skills'],
                'missing_skills'=> $mData['missing_skills'],
                'ai_recommendation'=> ['score_reason' => 'AI matched based on KRS skills'],
                'status'        => $mData['status'] ?? 'pending',
                'selected_at'   => isset($mData['status']) && $mData['status'] === 'selected' ? now()->subDays(rand(5,20)) : null,
            ]);
            $matchings[] = $matching;
        }

        // ============================================================
        // 6. PAYMENTS & WORKROOMS
        // ============================================================
        $paymentProjects = [
            ['project_idx'=>0, 'status'=>'released', 'amount'=>2500000, 'student_idx'=>0, 'client_idx'=>0, 'workroom_status'=>'approved', 'rating'=>5],
            ['project_idx'=>1, 'status'=>'held',     'amount'=>1200000, 'student_idx'=>1, 'client_idx'=>0, 'workroom_status'=>'active', 'rating'=>null],
            ['project_idx'=>3, 'status'=>'held',     'amount'=>2000000, 'student_idx'=>3, 'client_idx'=>1, 'workroom_status'=>'submitted', 'rating'=>null],
            ['project_idx'=>6, 'status'=>'held',     'amount'=>3500000, 'student_idx'=>0, 'client_idx'=>2, 'workroom_status'=>'revision', 'rating'=>null],
            ['project_idx'=>9, 'status'=>'released', 'amount'=>4000000, 'student_idx'=>2, 'client_idx'=>2, 'workroom_status'=>'approved', 'rating'=>4],
        ];

        foreach ($paymentProjects as $pData) {
            $project  = $projects[$pData['project_idx']];
            $student  = $students[$pData['student_idx']];
            $client   = $clients[$pData['client_idx']];
            $fee      = $pData['amount'] * 0.05;

            $payment = Payment::create([
                'project_id'    => $project->id,
                'client_id'     => $client->id,
                'student_id'    => $student->id,
                'payment_code'  => 'SS-' . strtoupper(substr(md5(uniqid()), 0, 10)),
                'amount'        => $pData['amount'],
                'platform_fee'  => $fee,
                'student_amount'=> $pData['amount'] - $fee,
                'status'        => $pData['status'],
                'payment_method'=> 'virtual_account',
                'held_at'       => now()->subDays(rand(3,15)),
                'released_at'   => $pData['status'] === 'released' ? now()->subDays(rand(1,5)) : null,
                'mock_callback_data' => [
                    'va_number'    => '8888' . rand(1000000000, 9999999999),
                    'bank'         => 'BCA',
                    'expired_at'   => now()->addHours(24)->toDateTimeString(),
                    'reference_id' => 'SS-' . strtoupper(substr(md5(rand()), 0, 8)),
                ],
            ]);

            // Create Workroom
            $messages = [
                [
                    'id'=>1, 'user_id'=>null, 'user_name'=>'System', 'role'=>'system',
                    'message'=>'🎉 Workroom dibuka! Mulai kerjakan proyek dan upload deliverable saat selesai.',
                    'created_at'=> now()->subDays(10)->toDateTimeString(), 'type'=>'system',
                ],
                [
                    'id'=>2, 'user_id'=>$student->user_id, 'user_name'=>$student->user->name, 'role'=>'mahasiswa',
                    'message'=>'Halo! Saya sudah mulai mengerjakan proyek ini. Estimasi selesai dalam 5 hari.',
                    'created_at'=> now()->subDays(9)->toDateTimeString(), 'type'=>'text',
                ],
                [
                    'id'=>3, 'user_id'=>$client->user_id, 'user_name'=>$client->user->name, 'role'=>'client',
                    'message'=>'Siap! Hubungi saya jika ada pertanyaan. Saya online setiap hari kerja.',
                    'created_at'=> now()->subDays(9)->toDateTimeString(), 'type'=>'text',
                ],
            ];

            if (in_array($pData['workroom_status'], ['submitted', 'approved'])) {
                $messages[] = [
                    'id'=>4, 'user_id'=>null, 'user_name'=>'System', 'role'=>'system',
                    'message'=>"📎 {$student->user->name} telah mengupload deliverable final.",
                    'created_at'=> now()->subDays(3)->toDateTimeString(), 'type'=>'system',
                ];
            }
            if ($pData['workroom_status'] === 'approved') {
                $messages[] = [
                    'id'=>5, 'user_id'=>null, 'user_name'=>'System', 'role'=>'system',
                    'message'=>'✅ Client telah menyetujui pekerjaan! Dana dicairkan ke mahasiswa.',
                    'created_at'=> now()->subDays(1)->toDateTimeString(), 'type'=>'approved',
                ];
            }

            $workroom = Workroom::create([
                'project_id'         => $project->id,
                'client_id'          => $client->id,
                'student_id'         => $student->id,
                'status'             => $pData['workroom_status'],
                'progress_percentage'=> match($pData['workroom_status']) {
                    'active'      => rand(20, 60),
                    'submitted'   => 90,
                    'revision'    => 70,
                    'approved'    => 100,
                    default       => 0,
                },
                'messages'           => $messages,
                'deliverables'       => in_array($pData['workroom_status'], ['submitted','approved']) ? [[
                    'id'=>1, 'filename'=>'deliverable_final.zip',
                    'path'=>'deliverables/'.$project->id.'/deliverable_final.zip',
                    'notes'=>'Semua fitur sudah diimplementasikan sesuai requirement.',
                    'uploaded_at'=> now()->subDays(3)->toDateTimeString(),
                    'uploaded_by'=> $student->user->name,
                ]] : [],
                'submitted_at'       => in_array($pData['workroom_status'], ['submitted','approved']) ? now()->subDays(3) : null,
                'approved_at'        => $pData['workroom_status'] === 'approved' ? now()->subDays(1) : null,
            ]);

            // Revision for revision status
            if ($pData['workroom_status'] === 'revision') {
                Revision::create([
                    'workroom_id'    => $workroom->id,
                    'requested_by'   => $client->user_id,
                    'revision_number'=> 1,
                    'feedback'       => 'Tolong perbaiki dokumentasi API dan tambahkan unit test untuk endpoint utama.',
                    'specific_changes'=> ['Dokumentasi API', 'Unit Testing', 'Error handling'],
                    'status'         => 'pending',
                    'requested_at'   => now()->subDays(2),
                ]);
            }

            // Portfolio for completed projects
            if ($pData['status'] === 'released') {
                $portfolio = Portfolio::create([
                    'student_id'   => $student->id,
                    'project_id'   => $project->id,
                    'title'        => $project->title,
                    'description'  => "Berhasil menyelesaikan {$project->title} untuk {$client->company_name}.",
                    'skills_used'  => $project->required_skills,
                    'earned_amount'=> $payment->student_amount,
                    'rating'       => $pData['rating'],
                    'client_company'=> $client->company_name,
                    'is_verified'  => true,
                    'is_public'    => true,
                    'completed_at' => now()->subDays(rand(1,5)),
                ]);

                // Update student stats
                $student->increment('total_projects');
                $student->increment('total_earnings', (float) $payment->student_amount);
                $student->update(['average_rating' => $pData['rating']]);
            }

            // Record payment fact
            FactProjectActivity::record([
                'project_id'       => $project->id,
                'client_id'        => $client->id,
                'student_id'       => $student->id,
                'payment_id'       => $payment->id,
                'activity_type'    => $pData['status'] === 'released' ? 'payment_released' : 'payment_held',
                'activity_category'=> 'payment',
                'amount'           => $payment->amount,
                'platform_fee'     => $payment->platform_fee,
                'payment_status'   => $pData['status'],
                'project_title'    => $project->title,
                'project_category' => $project->category,
                'rating'           => $pData['rating'],
            ]);
        }

        // ============================================================
        // 7. OLAP FACT TABLE - Historical Data for Analytics
        // ============================================================
        $categories    = ['Web Development', 'Data Science', 'Mobile Development', 'UI/UX Design', 'Machine Learning', 'IoT / Embedded', 'Backend Development'];
        $industries    = ['E-Commerce', 'F&B Tech', 'IT Services', 'Healthcare', 'Education', 'Fintech'];
        $universitas   = ['Universitas Indonesia', 'Institut Teknologi Bandung', 'Universitas Gadjah Mada', 'Universitas Brawijaya'];
        $activityTypes = ['project_created', 'candidate_selected', 'payment_held', 'payment_released', 'revision_requested', 'project_completed', 'krs_uploaded'];

        // Generate 60 historical records over 6 months
        for ($i = 0; $i < 60; $i++) {
            $daysAgo = rand(0, 180);
            $amount  = rand(5, 40) * 100000;
            $date    = now()->subDays($daysAgo);

            FactProjectActivity::create([
                'project_id'        => $projects[array_rand($projects)]->id,
                'client_id'         => $clients[array_rand($clients)]->id,
                'student_id'        => rand(0, 1) ? $students[array_rand($students)]->id : null,
                'activity_type'     => $activityTypes[array_rand($activityTypes)],
                'activity_category' => ['student','client','payment','project'][rand(0,3)],
                'match_score'       => rand(60, 98) / 100,
                'amount'            => $amount,
                'platform_fee'      => $amount * 0.05,
                'revision_count'    => rand(0, 3),
                'duration_days'     => rand(7, 60),
                'rating'            => rand(3, 5),
                'project_title'     => 'Proyek Demo ' . ($i + 1),
                'project_category'  => $categories[array_rand($categories)],
                'student_universitas'=> $universitas[array_rand($universitas)],
                'student_jurusan'   => ['Teknik Informatika','Sistem Informasi','Ilmu Komputer'][rand(0,2)],
                'client_industry'   => $industries[array_rand($industries)],
                'skills_involved'   => array_slice(['Laravel','Python','Flutter','React','Figma','PHP','MySQL','TensorFlow'], 0, rand(2,4)),
                'payment_status'    => ['pending','held','released'][rand(0,2)],
                'project_status'    => ['open','in_progress','completed','cancelled'][rand(0,3)],
                'activity_date'     => $date->toDateString(),
                'activity_month'    => $date->month,
                'activity_year'     => $date->year,
                'activity_quarter'  => 'Q' . $date->quarter,
            ]);
        }

        $this->command->info('✅ SkillSync Demo Data seeded successfully!');
        $this->command->info('');
        $this->command->info('📋 LOGIN CREDENTIALS:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('👤 Admin   : admin@skillsync.id / password');
        $this->command->info('🎓 Mahasiswa: budi@student.id / password');
        $this->command->info('🏢 Client  : andi@tokobaju.id / password');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
