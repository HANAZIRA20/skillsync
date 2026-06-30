<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use App\Models\FactProjectActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KRSController extends Controller
{
    public function __construct(private AIService $aiService) {}

    public function show()
    {
        $student = Auth::user()->student->load('skills');
        return view('student.krs', compact('student'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'krs_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $student = Auth::user()->student;

        // Store file
        $path = $request->file('krs_file')->store('krs', 'public');

        // Update student record
        $student->update([
            'krs_file_path' => $path,
            'krs_status' => 'uploaded',
        ]);

        // Run AI parsing (mock)
        $parsedResult = $this->aiService->parseKRS([
            'mata_kuliah' => $this->aiService->generateMockMataKuliah(),
            'semester' => $student->semester ?? rand(4, 8),
            'ipk' => $student->ipk ?? round(rand(280, 390) / 100, 2),
        ]);

        // Save skills to database
        $this->aiService->saveSkillsToStudent($student, $parsedResult);

        // Record to fact table
        FactProjectActivity::record([
            'student_id' => $student->id,
            'activity_type' => 'krs_uploaded',
            'activity_category' => 'student',
            'student_universitas' => $student->universitas,
            'student_jurusan' => $student->jurusan,
            'skills_involved' => array_column($parsedResult['skills'], 'name'),
        ]);

        return redirect()->route('student.krs')
            ->with('success', 'KRS berhasil diproses! AI telah mengekstrak ' . count($parsedResult['skills']) . ' skill dari KRS Anda.');
    }

    public function reparse()
    {
        $student = Auth::user()->student;

        if ($student->krs_status === 'not_uploaded') {
            return back()->with('error', 'Silakan upload KRS terlebih dahulu.');
        }

        $parsedResult = $this->aiService->parseKRS([
            'mata_kuliah' => $this->aiService->generateMockMataKuliah(),
            'semester' => $student->semester,
            'ipk' => $student->ipk,
        ]);

        $this->aiService->saveSkillsToStudent($student, $parsedResult);

        return redirect()->route('student.krs')
            ->with('success', 'AI berhasil memperbarui profil skill Anda!');
    }
}
