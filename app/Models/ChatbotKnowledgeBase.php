<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotKnowledgeBase extends Model
{
    protected $table = 'chatbot_knowledge_bases';

    protected $fillable = [
        'category', 'question', 'answer', 'keywords',
        'language', 'priority', 'is_active', 'usage_count', 'sort_order',
    ];

    protected $casts = [
        'keywords'  => 'array',
        'is_active' => 'boolean',
    ];

    // ── Categories ──
    public const CATEGORIES = [
        'general'  => '📋 General',
        'modules'  => '📦 Modules & Features',
        'pricing'  => '💰 Pricing & Plans',
        'support'  => '🛠️ Technical Support',
        'demo'     => '🎯 Demo & Trial',
        'army'     => '🏛️ Army Authorization',
        'contact'  => '📞 Contact Information',
        'faq'      => '❓ FAQs',
    ];

    /**
     * Search knowledge base by user query.
     * Uses keywords + question matching.
     */
    public static function searchByQuery(string $query): array
    {
        $query = mb_strtolower($query);
        $entries = static::where('is_active', true)
            ->orderByDesc('priority')
            ->orderBy('sort_order')
            ->get();

        $results = [];

        foreach ($entries as $entry) {
            $score = 0;

            // Check keywords match
            if ($entry->keywords) {
                foreach ($entry->keywords as $keyword) {
                    if (mb_stripos($query, mb_strtolower($keyword)) !== false) {
                        $score += 10;
                    }
                }
            }

            // Check question similarity
            if (mb_stripos($query, mb_strtolower($entry->question)) !== false ||
                mb_stripos(mb_strtolower($entry->question), $query) !== false) {
                $score += 5;
            }

            // Check individual words from question
            $questionWords = preg_split('/\s+/u', mb_strtolower($entry->question));
            $queryWords = preg_split('/\s+/u', $query);
            foreach ($queryWords as $qw) {
                if (mb_strlen($qw) < 3) continue;
                foreach ($questionWords as $ew) {
                    if (mb_stripos($ew, $qw) !== false || mb_stripos($qw, $ew) !== false) {
                        $score += 2;
                    }
                }
            }

            if ($score > 0) {
                $results[] = [
                    'entry' => $entry,
                    'score' => $score + $entry->priority,
                ];
            }
        }

        // Sort by score descending
        usort($results, fn ($a, $b) => $b['score'] <=> $a['score']);

        return $results;
    }

    /**
     * Get knowledge context for AI prompt.
     * Returns top matching Q&A pairs as text for Gemini.
     */
    public static function getContextForAI(string $query, int $maxEntries = 5): string
    {
        $results = static::searchByQuery($query);
        $topResults = array_slice($results, 0, $maxEntries);

        if (empty($topResults)) {
            // Return all active entries as general context
            $allEntries = static::where('is_active', true)
                ->orderByDesc('priority')
                ->take($maxEntries * 2)
                ->get();

            if ($allEntries->isEmpty()) {
                return '';
            }

            $context = "## Knowledge Base:\n";
            foreach ($allEntries as $entry) {
                $context .= "Q: {$entry->question}\nA: {$entry->answer}\n\n";
            }
            return $context;
        }

        $context = "## Relevant Knowledge Base Entries:\n";
        foreach ($topResults as $item) {
            $entry = $item['entry'];
            $context .= "Q: {$entry->question}\nA: {$entry->answer}\n\n";

            // Increment usage count
            $entry->increment('usage_count');
        }

        return $context;
    }

    /**
     * Seed default knowledge base entries.
     */
    public static function seedDefaults(): void
    {
        $defaults = [
            [
                'category' => 'general',
                'question' => 'TrustedU ERP কী?',
                'answer' => 'TrustedU ERP হলো বাংলাদেশ সেনাবাহিনী কর্তৃক অনুমোদিত একটি সমন্বিত শিক্ষা ব্যবস্থাপনা প্ল্যাটফর্ম। এটি ক্যান্টনমেন্ট পাবলিক স্কুল ও কলেজগুলোর জন্য তৈরি। বর্তমানে ১৭টি ক্যাম্পাসে সক্রিয় এবং মোট ৬৩টি প্রতিষ্ঠানে সম্প্রসারণ হচ্ছে।',
                'keywords' => ['trustedu', 'erp', 'কী', 'what', 'about', 'সম্পর্কে', 'পরিচিতি'],
                'language' => 'both',
                'priority' => 10,
                'sort_order' => 1,
            ],
            [
                'category' => 'modules',
                'question' => 'কী কী মডিউল আছে?',
                'answer' => "TrustedU ERP-তে রয়েছে:\n📚 Student Management — ছাত্র তথ্য ব্যবস্থাপনা\n📊 Exam & Result — পরীক্ষা ও ফলাফল প্রক্রিয়াকরণ\n💰 Accounting — আর্থিক ব্যবস্থাপনা\n👨‍🏫 HR & Payroll — মানব সম্পদ ও বেতন\n📱 SMS Gateway — এসএমএস নোটিফিকেশন\n🏫 Online Admission — অনলাইন ভর্তি\n📖 Library Management — লাইব্রেরি\n🚌 Transport Management — পরিবহন\n🏠 Hostel Management — হোস্টেল\n👨‍👩‍👧 Parent Portal — অভিভাবক পোর্টাল\n📋 Attendance — উপস্থিতি ব্যবস্থাপনা",
                'keywords' => ['module', 'মডিউল', 'feature', 'ফিচার', 'সুবিধা', 'কী কী', 'কি কি', 'functionality'],
                'language' => 'both',
                'priority' => 9,
                'sort_order' => 2,
            ],
            [
                'category' => 'pricing',
                'question' => 'দাম কত? প্রাইসিং কী?',
                'answer' => 'আমাদের প্রতিটি প্রতিষ্ঠানের জন্য কাস্টমাইজড প্যাকেজ রয়েছে। প্রতিষ্ঠানের আকার, ছাত্রসংখ্যা এবং প্রয়োজনীয় মডিউল অনুযায়ী দাম নির্ধারিত হয়। বিস্তারিত জানতে একটি ডেমো বুক করুন অথবা আমাদের সেলস টিমে যোগাযোগ করুন: info@tilbd.net',
                'keywords' => ['price', 'pricing', 'cost', 'দাম', 'খরচ', 'মূল্য', 'প্যাকেজ', 'package', 'rate', 'charge'],
                'language' => 'both',
                'priority' => 8,
                'sort_order' => 3,
            ],
            [
                'category' => 'demo',
                'question' => 'কীভাবে ডেমো বুক করব?',
                'answer' => 'ডেমো বুক করতে আমাদের ওয়েবসাইটের "Book a Free Demo" বাটনে ক্লিক করুন। আপনার নাম, প্রতিষ্ঠানের নাম এবং যোগাযোগের তথ্য দিন। আমাদের টিম ২৪ ঘণ্টার মধ্যে আপনার সাথে যোগাযোগ করবে।',
                'keywords' => ['demo', 'ডেমো', 'দেখতে', 'দেখান', 'trial', 'ট্রায়াল', 'book', 'বুক', 'free'],
                'language' => 'both',
                'priority' => 8,
                'sort_order' => 4,
            ],
            [
                'category' => 'army',
                'question' => 'বাংলাদেশ সেনাবাহিনীর অনুমোদন আছে?',
                'answer' => 'হ্যাঁ! TrustedU ERP বাংলাদেশ সেনাবাহিনী কর্তৃক আনুষ্ঠানিকভাবে অনুমোদিত। এটি সকল ক্যান্টনমেন্ট পাবলিক স্কুল ও কলেজে ব্যবহারের জন্য মনোনীত। বর্তমানে ১৭টি ক্যাম্পাসে লাইভ এবং ৬৩টি প্রতিষ্ঠানে সম্প্রসারণ চলছে।',
                'keywords' => ['army', 'আর্মি', 'সেনা', 'ক্যান্টনমেন্ট', 'cantonment', 'authorized', 'অনুমোদিত', 'military'],
                'language' => 'both',
                'priority' => 7,
                'sort_order' => 5,
            ],
            [
                'category' => 'contact',
                'question' => 'যোগাযোগের তথ্য কী?',
                'answer' => "যোগাযোগ করুন:\n📧 Email: info@tilbd.net\n🌐 Website: trustedu.edu.bd\n🏢 Developer: Trust Innovation Ltd (TILBD)\n\nঅফিস আওয়ারে কল বা ইমেইল করতে পারেন। আমাদের সাপোর্ট টিম সর্বদা আপনার সেবায় প্রস্তুত।",
                'keywords' => ['contact', 'যোগাযোগ', 'ফোন', 'phone', 'email', 'ইমেইল', 'address', 'ঠিকানা'],
                'language' => 'both',
                'priority' => 6,
                'sort_order' => 6,
            ],
            [
                'category' => 'support',
                'question' => 'টেকনিক্যাল সাপোর্ট কীভাবে পাব?',
                'answer' => 'টেকনিক্যাল সাপোর্টের জন্য info@tilbd.net এ ইমেইল করুন অথবা আপনার সমস্যার বিবরণ এখানে চ্যাটে জানান। জরুরি সমস্যায় আমাদের সাপোর্ট টিম সরাসরি সহায়তা প্রদান করবে।',
                'keywords' => ['support', 'help', 'সমস্যা', 'সাহায্য', 'হেল্প', 'সাপোর্ট', 'problem', 'issue', 'bug', 'error'],
                'language' => 'both',
                'priority' => 7,
                'sort_order' => 7,
            ],
            [
                'category' => 'faq',
                'question' => 'কোন ডিভাইসে কাজ করে?',
                'answer' => 'TrustedU ERP পূর্ণ মোবাইল রেসপন্সিভ। ডেস্কটপ, ল্যাপটপ, ট্যাবলেট এবং মোবাইল — সকল ডিভাইসে সমানভাবে কাজ করে। যেকোনো আধুনিক ব্রাউজারে (Chrome, Firefox, Safari, Edge) ব্যবহার করা যায়।',
                'keywords' => ['device', 'mobile', 'ডিভাইস', 'মোবাইল', 'responsive', 'phone', 'tablet', 'browser'],
                'language' => 'both',
                'priority' => 5,
                'sort_order' => 8,
            ],
        ];

        foreach ($defaults as $entry) {
            static::firstOrCreate(
                ['question' => $entry['question']],
                $entry
            );
        }
    }
}
