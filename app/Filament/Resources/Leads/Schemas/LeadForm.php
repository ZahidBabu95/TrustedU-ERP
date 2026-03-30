<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\Lead;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Lead')
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->schema([

                    // ━━ TAB 1: Basic Info ━━
                    Tab::make('Lead Info')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Section::make('Contact Information')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255)
                                        ->prefixIcon('heroicon-o-user')
                                        ->label('Contact Name'),
                                    TextInput::make('company')
                                        ->maxLength(255)
                                        ->placeholder('Company / Organization')
                                        ->prefixIcon('heroicon-o-building-office'),
                                    TextInput::make('email')
                                        ->email()
                                        ->maxLength(255)
                                        ->prefixIcon('heroicon-o-envelope'),
                                    TextInput::make('phone')
                                        ->tel()
                                        ->maxLength(30)
                                        ->prefixIcon('heroicon-o-phone'),
                                    TextInput::make('contact_person')
                                        ->maxLength(255)
                                        ->prefixIcon('heroicon-o-user-circle')
                                        ->label('Primary Contact Person'),
                                    TextInput::make('address')
                                        ->maxLength(500)
                                        ->prefixIcon('heroicon-o-map-pin')
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    // ━━ TAB 2: Institute Details ━━
                    Tab::make('Institute')
                        ->icon('heroicon-o-academic-cap')
                        ->schema([
                            Section::make('Institute Information')
                                ->columns(2)
                                ->description('প্রতিষ্ঠানের বিস্তারিত তথ্য')
                                ->schema([
                                    TextInput::make('institute_name')
                                        ->maxLength(255)
                                        ->prefixIcon('heroicon-o-building-library')
                                        ->label('Institute Name'),
                                    Select::make('institute_type')
                                        ->options(Lead::INSTITUTE_TYPE_LABELS)
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-academic-cap')
                                        ->label('Institute Type'),
                                    TextInput::make('student_count')
                                        ->numeric()
                                        ->minValue(0)
                                        ->prefixIcon('heroicon-o-users')
                                        ->label('Number of Students'),
                                ]),
                        ]),

                    // ━━ TAB 3: Lead Source ━━
                    Tab::make('Lead Source')
                        ->icon('heroicon-o-signal')
                        ->schema([
                            Section::make('Source Information')
                                ->description('লীড কোথা থেকে এসেছে তার বিস্তারিত')
                                ->schema([
                                    Select::make('source')
                                        ->options(Lead::SOURCE_LABELS)
                                        ->default('web')
                                        ->native(false)
                                        ->live()
                                        ->prefixIcon('heroicon-o-signal')
                                        ->label('Lead Source')
                                        ->columnSpanFull(),
                                ]),

                            // ── Facebook Source ──
                            Section::make('Facebook Details')
                                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                                ->description('ফেসবুক পেজ / অ্যাড থেকে আসা লীডের তথ্য')
                                ->columns(2)
                                ->visible(fn ($get) => $get('source') === 'facebook')
                                ->schema([
                                    TextInput::make('source_details.fb_page_name')
                                        ->label('Facebook Page Name')
                                        ->placeholder('e.g. TrustedU ERP Solutions')
                                        ->prefixIcon('heroicon-o-flag')
                                        ->maxLength(255),
                                    TextInput::make('source_details.fb_ad_id')
                                        ->label('Ad ID / Post ID')
                                        ->placeholder('e.g. 23856789012345')
                                        ->prefixIcon('heroicon-o-hashtag')
                                        ->maxLength(255),
                                    TextInput::make('source_details.fb_campaign_name')
                                        ->label('Campaign / Ad Set Name')
                                        ->placeholder('e.g. ERP Lead Gen - March 2026')
                                        ->prefixIcon('heroicon-o-megaphone')
                                        ->maxLength(255),
                                    DateTimePicker::make('source_details.fb_message_date')
                                        ->label('Message / Inquiry Date')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-calendar'),
                                    TextInput::make('source_details.fb_ad_url')
                                        ->label('Ad / Post URL')
                                        ->placeholder('https://facebook.com/...')
                                        ->prefixIcon('heroicon-o-link')
                                        ->url()
                                        ->columnSpanFull()
                                        ->maxLength(500),
                                    Textarea::make('source_details.fb_notes')
                                        ->label('Facebook Notes')
                                        ->placeholder('মেসেজের বিষয়বস্তু বা অতিরিক্ত তথ্য...')
                                        ->rows(2)
                                        ->columnSpanFull(),
                                ]),

                            // ── Referral Source ──
                            Section::make('Referral Details')
                                ->icon('heroicon-o-user-plus')
                                ->description('কে রেফার করেছে তার তথ্য')
                                ->columns(2)
                                ->visible(fn ($get) => $get('source') === 'referral')
                                ->schema([
                                    TextInput::make('source_details.referrer_name')
                                        ->label('Referrer Name')
                                        ->placeholder('যিনি রেফার করেছেন')
                                        ->prefixIcon('heroicon-o-user')
                                        ->maxLength(255),
                                    TextInput::make('source_details.referrer_phone')
                                        ->label('Referrer Phone')
                                        ->tel()
                                        ->prefixIcon('heroicon-o-phone')
                                        ->maxLength(30),
                                    TextInput::make('source_details.referrer_email')
                                        ->label('Referrer Email')
                                        ->email()
                                        ->prefixIcon('heroicon-o-envelope')
                                        ->maxLength(255),
                                    Select::make('source_details.referrer_relation')
                                        ->label('Relationship')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-link')
                                        ->options([
                                            'existing_client' => 'Existing Client',
                                            'partner'         => 'Business Partner',
                                            'employee'        => 'Employee',
                                            'friend'          => 'Friend / Family',
                                            'other'           => 'Other',
                                        ]),
                                    TextInput::make('source_details.referrer_institution')
                                        ->label('Referrer Institution')
                                        ->placeholder('প্রতিষ্ঠানের নাম')
                                        ->prefixIcon('heroicon-o-building-office')
                                        ->maxLength(255),
                                    Textarea::make('source_details.referral_notes')
                                        ->label('Referral Notes')
                                        ->placeholder('রেফারেন্স সম্পর্কে অতিরিক্ত তথ্য...')
                                        ->rows(2)
                                        ->columnSpanFull(),
                                ]),

                            // ── Cold Call Source ──
                            Section::make('Cold Call Details')
                                ->icon('heroicon-o-phone-arrow-up-right')
                                ->description('কোল্ড কল এর বিস্তারিত')
                                ->columns(2)
                                ->visible(fn ($get) => $get('source') === 'cold_call')
                                ->schema([
                                    TextInput::make('source_details.caller_name')
                                        ->label('Caller Name (Our Rep)')
                                        ->placeholder('যিনি কল করেছেন')
                                        ->prefixIcon('heroicon-o-user')
                                        ->maxLength(255),
                                    DateTimePicker::make('source_details.call_datetime')
                                        ->label('Call Date & Time')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-calendar'),
                                    TextInput::make('source_details.call_duration')
                                        ->label('Call Duration (minutes)')
                                        ->numeric()
                                        ->suffix('min')
                                        ->prefixIcon('heroicon-o-clock'),
                                    Select::make('source_details.call_outcome')
                                        ->label('Call Outcome')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-check-circle')
                                        ->options([
                                            'interested'     => 'Interested',
                                            'callback'       => 'Requested Callback',
                                            'not_available'  => 'Not Available',
                                            'not_interested' => 'Not Interested',
                                            'follow_up'      => 'Follow-up Needed',
                                        ]),
                                    Textarea::make('source_details.call_notes')
                                        ->label('Call Notes')
                                        ->placeholder('কলের বিষয়বস্তু ও ফলাফল...')
                                        ->rows(2)
                                        ->columnSpanFull(),
                                ]),

                            // ── Email Source ──
                            Section::make('Email Details')
                                ->icon('heroicon-o-envelope')
                                ->description('ইমেইল থেকে আসা লীডের তথ্য')
                                ->columns(2)
                                ->visible(fn ($get) => $get('source') === 'email')
                                ->schema([
                                    TextInput::make('source_details.email_sender')
                                        ->label('Sender Email')
                                        ->email()
                                        ->prefixIcon('heroicon-o-envelope')
                                        ->maxLength(255),
                                    TextInput::make('source_details.email_subject')
                                        ->label('Email Subject')
                                        ->prefixIcon('heroicon-o-document-text')
                                        ->maxLength(500),
                                    DateTimePicker::make('source_details.email_received_at')
                                        ->label('Received Date & Time')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-calendar'),
                                    Select::make('source_details.email_type')
                                        ->label('Email Type')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-inbox')
                                        ->options([
                                            'inquiry'   => 'Product Inquiry',
                                            'demo'      => 'Demo Request',
                                            'pricing'   => 'Pricing Request',
                                            'support'   => 'Support Query',
                                            'other'     => 'Other',
                                        ]),
                                    Textarea::make('source_details.email_body_summary')
                                        ->label('Email Summary')
                                        ->placeholder('ইমেইলের সারসংক্ষেপ...')
                                        ->rows(2)
                                        ->columnSpanFull(),
                                ]),

                            // ── Social Media Source ──
                            Section::make('Social Media Details')
                                ->icon('heroicon-o-globe-alt')
                                ->description('সোশ্যাল মিডিয়া থেকে আসা লীডের তথ্য')
                                ->columns(2)
                                ->visible(fn ($get) => $get('source') === 'social')
                                ->schema([
                                    Select::make('source_details.social_platform')
                                        ->label('Platform')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-globe-alt')
                                        ->options([
                                            'linkedin'  => 'LinkedIn',
                                            'twitter'   => 'Twitter / X',
                                            'instagram' => 'Instagram',
                                            'youtube'   => 'YouTube',
                                            'tiktok'    => 'TikTok',
                                            'other'     => 'Other',
                                        ]),
                                    TextInput::make('source_details.social_profile_url')
                                        ->label('Profile / Page URL')
                                        ->url()
                                        ->prefixIcon('heroicon-o-link')
                                        ->maxLength(500),
                                    TextInput::make('source_details.social_post_url')
                                        ->label('Post / Ad URL')
                                        ->url()
                                        ->prefixIcon('heroicon-o-link')
                                        ->maxLength(500),
                                    DateTimePicker::make('source_details.social_inquiry_date')
                                        ->label('Inquiry Date')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-calendar'),
                                    Textarea::make('source_details.social_notes')
                                        ->label('Social Media Notes')
                                        ->rows(2)
                                        ->columnSpanFull(),
                                ]),

                            // ── Website Source (Auto) ──
                            Section::make('Website Source')
                                ->icon('heroicon-o-computer-desktop')
                                ->description('ওয়েবসাইট থেকে স্বয়ংক্রিয়ভাবে আসা লীড')
                                ->columns(2)
                                ->visible(fn ($get) => $get('source') === 'web')
                                ->schema([
                                    TextInput::make('source_details.web_page_url')
                                        ->label('Landing Page URL')
                                        ->url()
                                        ->prefixIcon('heroicon-o-link')
                                        ->placeholder('যে পেজ থেকে ফর্ম সাবমিট হয়েছে')
                                        ->maxLength(500),
                                    TextInput::make('source_details.web_form_name')
                                        ->label('Form Name')
                                        ->prefixIcon('heroicon-o-document-text')
                                        ->placeholder('e.g. Contact Form, Demo Request')
                                        ->maxLength(255),
                                    DateTimePicker::make('source_details.web_submitted_at')
                                        ->label('Submitted At')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-calendar'),
                                    TextInput::make('source_details.web_ip')
                                        ->label('IP Address')
                                        ->prefixIcon('heroicon-o-globe-alt')
                                        ->placeholder('Auto-captured')
                                        ->maxLength(45),
                                ]),

                            // ── Chatbot Source (Auto) ──
                            Section::make('Chatbot Source')
                                ->icon('heroicon-o-chat-bubble-bottom-center-text')
                                ->description('চ্যাটবট থেকে স্বয়ংক্রিয়ভাবে আসা লীড')
                                ->columns(2)
                                ->visible(fn ($get) => $get('source') === 'chatbot')
                                ->schema([
                                    TextInput::make('source_details.chatbot_session_id')
                                        ->label('Chat Session ID')
                                        ->prefixIcon('heroicon-o-hashtag')
                                        ->placeholder('Auto-generated')
                                        ->maxLength(255),
                                    DateTimePicker::make('source_details.chatbot_started_at')
                                        ->label('Chat Started At')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-calendar'),
                                    TextInput::make('source_details.chatbot_page_url')
                                        ->label('Chat Initiated From')
                                        ->url()
                                        ->prefixIcon('heroicon-o-link')
                                        ->placeholder('যে পেজে চ্যাট শুরু হয়েছে')
                                        ->columnSpanFull()
                                        ->maxLength(500),
                                ]),

                            // ── Other Source ──
                            Section::make('Other Source Details')
                                ->icon('heroicon-o-ellipsis-horizontal-circle')
                                ->description('অন্যান্য সোর্স এর বিবরণ')
                                ->visible(fn ($get) => $get('source') === 'other')
                                ->schema([
                                    TextInput::make('source_details.other_source_name')
                                        ->label('Source Name')
                                        ->prefixIcon('heroicon-o-tag')
                                        ->placeholder('সোর্সের নাম লিখুন')
                                        ->maxLength(255),
                                    Textarea::make('source_details.other_description')
                                        ->label('Description')
                                        ->placeholder('এই লীড কিভাবে এসেছে তার বিবরণ...')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    // ━━ TAB 4: Pipeline & Assignment ━━
                    Tab::make('Pipeline')
                        ->icon('heroicon-o-funnel')
                        ->schema([
                            Section::make('Pipeline & Assignment')
                                ->columns(2)
                                ->schema([
                                    Select::make('status')
                                        ->options(Lead::STATUS_LABELS)
                                        ->default('new')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-signal'),
                                    Select::make('pipeline_stage')
                                        ->options(Lead::PIPELINE_STAGE_LABELS)
                                        ->default('new_lead')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-funnel')
                                        ->label('CRM Pipeline Stage'),
                                    Select::make('interest_level')
                                        ->options(Lead::INTEREST_LABELS)
                                        ->default('warm')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-fire')
                                        ->label('Interest Level'),
                                    Select::make('priority')
                                        ->options(Lead::PRIORITY_LABELS)
                                        ->default('medium')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-flag'),
                                    Select::make('assigned_to')
                                        ->label('Assign To')
                                        ->options(User::where('is_active', true)->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->prefixIcon('heroicon-o-user-circle'),
                                    Select::make('team_id')
                                        ->label('Assign Team')
                                        ->options(\App\Models\Team::where('is_active', true)->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->prefixIcon('heroicon-o-user-group'),
                                    TextInput::make('value')
                                        ->numeric()
                                        ->prefix('৳')
                                        ->label('Lead Value'),
                                    DatePicker::make('expected_close_date')
                                        ->label('Expected Close Date')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-calendar'),
                                    TextInput::make('qualification_score')
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->suffix('/100')
                                        ->disabled()
                                        ->label('Qualification Score')
                                        ->prefixIcon('heroicon-o-chart-bar'),
                                ]),
                        ]),

                    // ━━ TAB 5: Follow-up ━━
                    Tab::make('Follow-up')
                        ->icon('heroicon-o-calendar-days')
                        ->schema([
                            Section::make('Follow-up & Notes')
                                ->columns(2)
                                ->schema([
                                    DatePicker::make('follow_up_date')
                                        ->label('Next Follow-up Date')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-calendar'),
                                    TextInput::make('label')
                                        ->maxLength(100)
                                        ->placeholder('e.g. Hot Lead, Review Needed')
                                        ->prefixIcon('heroicon-o-tag'),
                                    Textarea::make('follow_up_notes')
                                        ->rows(3)
                                        ->label('Follow-up Notes')
                                        ->columnSpanFull(),
                                    Textarea::make('notes')
                                        ->rows(4)
                                        ->label('General Notes')
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    // ━━ TAB 6: Lost Info (visible only when lost) ━━
                    Tab::make('Lost')
                        ->icon('heroicon-o-x-circle')
                        ->visible(fn ($record) => $record?->status === 'lost' || $record?->pipeline_stage === 'lost')
                        ->schema([
                            Section::make('Lost Information')
                                ->schema([
                                    TextInput::make('lost_reason')
                                        ->maxLength(500)
                                        ->label('Reason for Loss')
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ]),
        ]);
    }
}
