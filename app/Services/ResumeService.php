<?php

namespace App\Services;

use App\Models\JobPost;
use App\Models\Resume;
use Smalot\PdfParser\Parser as PdfParser;

class ResumeService
{
    /**
     * Known skills/keywords the portal recognises in a resume. Lowercase.
     * Extend freely; matching is plain substring on word boundaries.
     */
    public const SKILLS = [
        // Technology
        'php', 'laravel', 'javascript', 'typescript', 'react', 'vue', 'node', 'python',
        'java', 'c#', '.net', 'sql', 'mysql', 'postgresql', 'html', 'css', 'tailwind',
        'git', 'docker', 'aws', 'azure', 'api', 'wordpress',
        // Office / admin
        'excel', 'word', 'powerpoint', 'microsoft office', 'data entry', 'administration',
        'scheduling', 'reception', 'bookkeeping', 'quickbooks', 'tally', 'accounting',
        'payroll', 'invoicing',
        // Sales / customer
        'sales', 'customer service', 'retail', 'marketing', 'social media', 'crm',
        'negotiation', 'cashier',
        // Trades / logistics
        'driving', 'forklift', 'warehouse', 'logistics', 'carpentry', 'plumbing',
        'electrical', 'painting', 'maintenance', 'welding', 'hvac',
        // Healthcare / care
        'nursing', 'first aid', 'caregiving', 'childcare', 'pharmacy', 'phlebotomy',
        // Hospitality / food
        'cooking', 'barista', 'waiter', 'hospitality', 'housekeeping', 'catering',
        // Education / general
        'teaching', 'tutoring', 'translation', 'arabic', 'tamil', 'english',
        'communication', 'leadership', 'project management', 'time management',
    ];

    /** Extract plain text from an uploaded resume file. */
    public function extractText(string $absolutePath, ?string $mime, string $originalName): string
    {
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        try {
            if ($ext === 'pdf' || $mime === 'application/pdf') {
                return (new PdfParser())->parseFile($absolutePath)->getText();
            }

            if ($ext === 'docx') {
                return $this->extractDocx($absolutePath);
            }

            // txt, or anything else we can read as text.
            return (string) file_get_contents($absolutePath);
        } catch (\Throwable $e) {
            return '';
        }
    }

    /** Pull readable text out of a .docx (zip of XML) without extra libraries. */
    private function extractDocx(string $path): string
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            return '';
        }
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        if ($xml === false) {
            return '';
        }
        // Paragraph/line breaks to spaces, then strip tags.
        $xml = preg_replace('/<\/w:p>/', "\n", $xml);
        return trim(html_entity_decode(strip_tags($xml), ENT_QUOTES | ENT_XML1, 'UTF-8'));
    }

    /** Detect known skills present in free text. Returns lowercase skills. */
    public function detectSkills(string $text): array
    {
        $haystack = ' ' . strtolower(preg_replace('/\s+/', ' ', $text)) . ' ';
        $found = [];
        foreach (self::SKILLS as $skill) {
            // word-ish boundary match; handles multi-word skills too.
            $pattern = '/(?<![a-z0-9])' . preg_quote($skill, '/') . '(?![a-z0-9])/i';
            if (preg_match($pattern, $haystack)) {
                $found[] = $skill;
            }
        }
        return array_values(array_unique($found));
    }

    /**
     * Suggest open jobs for a resume, ranked by relevance.
     * Score = 3 points per matching skill + 1 point per job keyword found in the
     * resume text (title words, category, and listed skills).
     *
     * @return array<int, array{job: JobPost, score: int, matched: array<int,string>}>
     */
    public function suggestJobs(Resume $resume, int $limit = 10): array
    {
        $resumeSkills = $resume->skillList();
        $text = ' ' . strtolower(preg_replace('/\s+/', ' ', (string) $resume->parsed_text)) . ' ';

        $results = [];
        foreach (JobPost::where('status', 'open')->get() as $job) {
            $jobSkills = $job->skillList();
            $matched = array_values(array_intersect($resumeSkills, $jobSkills));
            $score = count($matched) * 3;

            // Keyword presence of job skills + category + title words in the resume.
            $keywords = array_merge(
                $jobSkills,
                [strtolower($job->category)],
                preg_split('/\s+/', strtolower($job->title))
            );
            foreach (array_unique(array_filter($keywords)) as $kw) {
                if (strlen($kw) < 3) {
                    continue;
                }
                $pattern = '/(?<![a-z0-9])' . preg_quote($kw, '/') . '(?![a-z0-9])/i';
                if (preg_match($pattern, $text)) {
                    $score += 1;
                    if (in_array($kw, $jobSkills, true) && ! in_array($kw, $matched, true)) {
                        $matched[] = $kw;
                    }
                }
            }

            if ($score > 0) {
                $results[] = ['job' => $job, 'score' => $score, 'matched' => array_values(array_unique($matched))];
            }
        }

        usort($results, fn ($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($results, 0, $limit);
    }
}
