<?php

namespace App\Services;

use App\Models\ChatbotKnowledgeBase;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatBotService
{
    /**
     * Build system prompt using Knowledge Base context.
     */
    private function getSystemPrompt(string $userMessage = ''): string
    {
        $botName = SystemSetting::get('chatbot_bot_name', 'TrustedU Assistant');

        // Get relevant knowledge base entries for context
        $knowledgeContext = ChatbotKnowledgeBase::getContextForAI($userMessage, 8);

        $basePrompt = <<<PROMPT
You are "{$botName}", a friendly and professional AI support bot for TrustedU ERP — Bangladesh Army Authorized Education Management Platform.

## Core Info:
- Authorized by Bangladesh Army for Cantonment Public Schools & Colleges
- Currently LIVE on 17 campuses, expanding to all 63 Cantonment institutions
- Developed by Trust Innovation Ltd (TILBD)
- Contact: info@tilbd.net

## Instructions:
1. Always be polite, helpful, and professional
2. Answer in the SAME language the user writes (Bengali/English)
3. If you don't know something specific, say so and suggest contacting the sales team
4. Encourage users to book a demo for detailed information
5. Keep responses concise (2-4 sentences max unless asked for detail)
6. If someone asks about pricing, suggest booking a demo or contacting sales
7. For technical support queries, collect their issue details and suggest creating a support ticket
8. ALWAYS prioritize information from the Knowledge Base below over general knowledge
PROMPT;

        if (!empty($knowledgeContext)) {
            $basePrompt .= "\n\n" . $knowledgeContext;
        }

        return $basePrompt;
    }

    /**
     * Get AI response — reads settings from SystemSetting.
     */
    public function getAIResponse(string $userMessage, array $conversationHistory = []): string
    {
        $aiEnabled = SystemSetting::get('chatbot_ai_enabled', true);
        $apiKey    = SystemSetting::get('chatbot_api_key');
        $provider  = SystemSetting::get('chatbot_ai_provider', 'gemini');
        $model     = SystemSetting::get('chatbot_ai_model', 'gemini-2.0-flash');

        // If AI is disabled or no API key, try knowledge base then rule-based fallback
        if (!$aiEnabled || empty($apiKey)) {
            return $this->getKnowledgeBaseResponse($userMessage)
                ?? $this->getRuleBasedResponse($userMessage);
        }

        try {
            if ($provider === 'gemini') {
                return $this->callGemini($apiKey, $model, $userMessage, $conversationHistory);
            }

            // Future: OpenAI, Claude support
            return $this->getKnowledgeBaseResponse($userMessage)
                ?? $this->getRuleBasedResponse($userMessage);

        } catch (\Exception $e) {
            Log::error('AI API error', ['error' => $e->getMessage(), 'provider' => $provider]);
            return $this->getKnowledgeBaseResponse($userMessage)
                ?? $this->getRuleBasedResponse($userMessage);
        }
    }

    /**
     * Call Google Gemini API.
     */
    private function callGemini(string $apiKey, string $model, string $userMessage, array $history): string
    {
        $systemPrompt = $this->getSystemPrompt($userMessage);

        // Build conversation context
        $contents = [];
        $recentHistory = array_slice($history, -10);
        foreach ($recentHistory as $msg) {
            $contents[] = [
                'role'  => $msg['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $msg['message']]],
            ];
        }

        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $userMessage]],
        ];

        $response = Http::timeout(15)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'system_instruction' => [
                    'parts' => [['text' => $systemPrompt]]
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature'    => 0.7,
                    'maxOutputTokens' => 400,
                    'topP'           => 0.9,
                ],
            ]);

        if ($response->successful()) {
            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if ($text) {
                return trim($text);
            }
        }

        Log::warning('Gemini API response issue', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        return $this->getKnowledgeBaseResponse($userMessage)
            ?? $this->getRuleBasedResponse($userMessage);
    }

    /**
     * Get response from Knowledge Base (no AI).
     */
    private function getKnowledgeBaseResponse(string $message): ?string
    {
        $results = ChatbotKnowledgeBase::searchByQuery($message);

        if (!empty($results)) {
            $topMatch = $results[0];
            if ($topMatch['score'] >= 5) {
                return $topMatch['entry']->answer;
            }
        }

        return null;
    }

    /**
     * Rule-based fallback when both AI and KB unavailable.
     */
    private function getRuleBasedResponse(string $message): string
    {
        $msg = mb_strtolower($message);

        if (preg_match('/(hello|hi|hey|আসসালামু|হ্যালো|হাই|সুপ্রভাত|শুভ)/u', $msg)) {
            return "আসসালামু আলাইকুম! 👋 TrustedU ERP-তে স্বাগতম। আমি আপনাকে কীভাবে সাহায্য করতে পারি?";
        }

        if (preg_match('/(price|pricing|cost|দাম|খরচ|মূল্য|প্যাকেজ|package)/u', $msg)) {
            return "আমাদের প্রতিটি প্রতিষ্ঠানের জন্য কাস্টমাইজড প্যাকেজ রয়েছে। বিস্তারিত জানতে ডেমো বুক করুন: info@tilbd.net 📧";
        }

        if (preg_match('/(demo|ডেমো|দেখতে|দেখান|trial|ট্রায়াল)/u', $msg)) {
            return "আমাদের ওয়েবসাইটের \"Book a Demo\" বাটনে ক্লিক করে ডেমো বুক করতে পারেন। আমাদের টিম আপনার সাথে যোগাযোগ করবে। 🎯";
        }

        if (preg_match('/(feature|module|মডিউল|ফিচার|সুবিধা|কী কী|কি কি)/u', $msg)) {
            return "TrustedU ERP-তে রয়েছে: 📚 Student Management, 📊 Exam & Result, 💰 Accounting, 👨‍🏫 HR & Payroll, 📱 SMS Gateway, 🏫 Online Admission এবং আরও অনেক কিছু!";
        }

        if (preg_match('/(contact|যোগাযোগ|ফোন|phone|email|ইমেইল)/u', $msg)) {
            return "📧 Email: info@tilbd.net\n📞 আমাদের ওয়েবসাইটে যোগাযোগের বিস্তারিত তথ্য পাবেন।";
        }

        if (preg_match('/(support|help|সমস্যা|সাহায্য|হেল্প|সাপোর্ট|problem|issue)/u', $msg)) {
            return "আপনার সমস্যার কথা বিস্তারিত জানান। জটিল সমস্যার জন্য সাপোর্ট টিকেট তৈরি করতে পারি। 🛠️";
        }

        if (preg_match('/(army|আর্মি|সেনা|ক্যান্টনমেন্ট|cantonment|authorized|অনুমোদিত)/u', $msg)) {
            return "TrustedU ERP বাংলাদেশ সেনাবাহিনী কর্তৃক অনুমোদিত। বর্তমানে ১৭টি ক্যাম্পাসে সক্রিয়, মোট ৬৩টি প্রতিষ্ঠানে সম্প্রসারণ হচ্ছে। 🏛️";
        }

        if (preg_match('/(thank|ধন্যবাদ|thanks|শুকরিয়া)/u', $msg)) {
            return "আপনাকেও ধন্যবাদ! 😊 আর কোনো প্রশ্ন থাকলে নিঃসংকোচে জিজ্ঞেস করুন।";
        }

        return "আপনার প্রশ্নের জন্য ধন্যবাদ! 😊 আমি TrustedU ERP সম্পর্কে যেকোনো প্রশ্নের উত্তর দিতে পারি — মডিউল, ফিচার, ডেমো বুকিং, বা সাপোর্ট। কীভাবে সাহায্য করতে পারি?";
    }
}
