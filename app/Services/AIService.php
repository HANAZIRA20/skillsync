<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentSkill;

class AIService
{
    /**
     * Mock AI Skill Database - berisi mata kuliah dan skill yang dihasilkan
     */
    private array $courseSkillMap = [
        // Programming
        'Pemrograman Web' => [['Laravel', 'Programming', 0.90], ['PHP', 'Programming', 0.88], ['HTML/CSS', 'Design', 0.85]],
        'Pemrograman Berorientasi Objek' => [['OOP', 'Programming', 0.92], ['Java', 'Programming', 0.85], ['Design Patterns', 'Programming', 0.75]],
        'Pemrograman Dasar' => [['Python', 'Programming', 0.80], ['Algoritma', 'Programming', 0.85], ['C++', 'Programming', 0.75]],
        'Pemrograman Mobile' => [['Android', 'Mobile', 0.88], ['Flutter', 'Mobile', 0.82], ['Kotlin', 'Mobile', 0.78]],
        'Pemrograman Python' => [['Python', 'Programming', 0.95], ['Data Analysis', 'Data Science', 0.80], ['Scripting', 'Programming', 0.75]],

        // Database
        'Basis Data' => [['SQL', 'Database', 0.92], ['Database Design', 'Database', 0.88], ['PostgreSQL', 'Database', 0.80]],
        'Sistem Manajemen Basis Data' => [['MySQL', 'Database', 0.90], ['PostgreSQL', 'Database', 0.85], ['Database Administration', 'Database', 0.82]],
        'Data Warehouse' => [['OLAP', 'Data Science', 0.88], ['ETL', 'Data Science', 0.85], ['Data Modeling', 'Data Science', 0.82]],

        // Networking
        'Jaringan Komputer' => [['Networking', 'Infrastructure', 0.88], ['TCP/IP', 'Infrastructure', 0.85], ['Network Security', 'Security', 0.75]],
        'Keamanan Sistem Informasi' => [['Cybersecurity', 'Security', 0.90], ['Network Security', 'Security', 0.85], ['Ethical Hacking', 'Security', 0.70]],

        // AI & Data Science
        'Kecerdasan Buatan' => [['Machine Learning', 'AI', 0.90], ['AI', 'AI', 0.92], ['Python', 'Programming', 0.82]],
        'Machine Learning' => [['Machine Learning', 'AI', 0.95], ['Python', 'Programming', 0.88], ['TensorFlow', 'AI', 0.80]],
        'Data Mining' => [['Data Mining', 'Data Science', 0.90], ['Data Analysis', 'Data Science', 0.88], ['Python', 'Programming', 0.82]],
        'Statistika' => [['Statistics', 'Data Science', 0.88], ['Data Analysis', 'Data Science', 0.82], ['R', 'Data Science', 0.75]],

        // Design
        'Interaksi Manusia Komputer' => [['UI/UX Design', 'Design', 0.90], ['User Research', 'Design', 0.85], ['Figma', 'Design', 0.80]],
        'Desain Grafis' => [['Graphic Design', 'Design', 0.92], ['Adobe Photoshop', 'Design', 0.88], ['Figma', 'Design', 0.85]],

        // Software Engineering
        'Rekayasa Perangkat Lunak' => [['Software Engineering', 'Engineering', 0.90], ['Agile', 'Engineering', 0.85], ['Git', 'Tools', 0.88]],
        'Analisis dan Perancangan Sistem' => [['System Analysis', 'Engineering', 0.88], ['UML', 'Engineering', 0.85], ['Project Management', 'Management', 0.75]],
        'Manajemen Proyek TI' => [['Project Management', 'Management', 0.90], ['Agile', 'Engineering', 0.88], ['Scrum', 'Engineering', 0.85]],

        // Business
        'Kewirausahaan' => [['Entrepreneurship', 'Business', 0.88], ['Business Development', 'Business', 0.82], ['Marketing', 'Business', 0.78]],
        'Pemasaran Digital' => [['Digital Marketing', 'Business', 0.92], ['SEO', 'Business', 0.88], ['Social Media Marketing', 'Business', 0.85]],
        'Sistem Informasi Manajemen' => [['ERP', 'Business', 0.82], ['Business Analysis', 'Business', 0.85], ['MIS', 'Business', 0.80]],
    ];

    /**
     * Parse KRS / Transkrip Akademik (Mock AI)
     * Returns extracted skill profile dari data akademik mahasiswa
     */
    public function parseKRS(array $krsData): array
    {
        $extractedSkills = [];
        $availableSchedule = [];
        $mataKuliahList = $krsData['mata_kuliah'] ?? $this->generateMockMataKuliah();

        // Extract skills dari mata kuliah
        foreach ($mataKuliahList as $mk) {
            $namaMK = $mk['nama'] ?? $mk;
            foreach ($this->courseSkillMap as $course => $skills) {
                if (stripos($namaMK, $course) !== false || stripos($course, $namaMK) !== false) {
                    foreach ($skills as $skillData) {
                        [$skillName, $category, $baseScore] = $skillData;
                        $nilai = $mk['nilai'] ?? 'B';
                        $scoreMultiplier = match($nilai) {
                            'A' => 1.0, 'A-' => 0.95,
                            'B+' => 0.90, 'B' => 0.85, 'B-' => 0.80,
                            'C+' => 0.70, 'C' => 0.65,
                            default => 0.75,
                        };
                        $finalScore = min(1.0, $baseScore * $scoreMultiplier);

                        if (!isset($extractedSkills[$skillName]) || $extractedSkills[$skillName]['score'] < $finalScore) {
                            $extractedSkills[$skillName] = [
                                'name' => $skillName,
                                'category' => $category,
                                'score' => $finalScore,
                                'evidence' => [$namaMK . ' (' . ($mk['nilai'] ?? 'B') . ')'],
                            ];
                        } else {
                            $extractedSkills[$skillName]['evidence'][] = $namaMK . ' (' . ($mk['nilai'] ?? 'B') . ')';
                        }
                    }
                    break;
                }
            }
        }

        // Sort by confidence score
        uasort($extractedSkills, fn($a, $b) => $b['score'] <=> $a['score']);

        // Generate jadwal kosong (mock)
        $availableSchedule = $this->detectAvailableSchedule($krsData['jadwal'] ?? []);

        return [
            'skills' => array_values($extractedSkills),
            'available_schedule' => $availableSchedule,
            'total_sks' => $krsData['total_sks'] ?? rand(120, 145),
            'ipk' => $krsData['ipk'] ?? round(rand(280, 395) / 100, 2),
            'semester' => $krsData['semester'] ?? rand(4, 8),
            'summary' => $this->generateAISummary($extractedSkills),
        ];
    }

    /**
     * Save parsed skills ke database
     */
    public function saveSkillsToStudent(Student $student, array $parsedResult): void
    {
        // Hapus skills lama
        $student->skills()->delete();

        foreach ($parsedResult['skills'] as $skill) {
            StudentSkill::create([
                'student_id' => $student->id,
                'skill_name' => $skill['name'],
                'category' => $skill['category'],
                'confidence_score' => $skill['score'],
                'source' => 'ai_parsed',
                'evidence' => $skill['evidence'] ?? [],
            ]);
        }

        // Update student profile
        $student->update([
            'skill_profile' => $parsedResult['skills'],
            'available_schedule' => $parsedResult['available_schedule'],
            'ipk' => $parsedResult['ipk'],
            'semester' => $parsedResult['semester'],
            'krs_status' => 'parsed',
        ]);
    }

    /**
     * Generate jadwal kosong mahasiswa
     */
    private function detectAvailableSchedule(array $jadwal): array
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $slots = ['08:00-10:00', '10:00-12:00', '13:00-15:00', '15:00-17:00', '19:00-21:00'];

        $available = [];
        foreach ($days as $day) {
            // Mock: 60% chance each slot is free
            $freeSlots = [];
            foreach ($slots as $slot) {
                if (rand(0, 100) > 40) {
                    $freeSlots[] = $slot;
                }
            }
            if (!empty($freeSlots)) {
                $available[] = ['hari' => $day, 'slots' => $freeSlots];
            }
        }

        return $available;
    }

    /**
     * Generate AI summary dari extracted skills
     */
    private function generateAISummary(array $skills): string
    {
        $topSkills = array_slice(array_keys($skills), 0, 3);
        $skillStr = implode(', ', $topSkills);

        $summaries = [
            "Mahasiswa ini memiliki kemampuan kuat dalam {$skillStr}. Profil akademik menunjukkan kesiapan untuk proyek-proyek teknis.",
            "Berdasarkan analisis KRS, kandidat menunjukkan keahlian utama di {$skillStr} dengan rekam jejak akademis yang baik.",
            "AI SkillSync mendeteksi profil kemampuan yang solid dalam {$skillStr}, cocok untuk proyek-proyek yang memerlukan keahlian tersebut.",
        ];

        return $summaries[array_rand($summaries)];
    }

    /**
     * Generate mock mata kuliah untuk demo
     */
    public function generateMockMataKuliah(): array
    {
        $allCourses = array_keys($this->courseSkillMap);
        shuffle($allCourses);
        $selected = array_slice($allCourses, 0, rand(8, 14));
        $grades = ['A', 'A', 'A-', 'B+', 'B', 'B', 'B', 'B-', 'C+'];

        return array_map(fn($course) => [
            'nama' => $course,
            'sks' => rand(2, 4),
            'nilai' => $grades[array_rand($grades)],
        ], $selected);
    }
}
