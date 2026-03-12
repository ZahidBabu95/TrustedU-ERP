<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;

class SeedPrivacyPolicy extends Command
{
    protected $signature   = 'seed:privacy-policy';
    protected $description = 'Insert privacy policy setting';

    public function handle(): void
    {
        $content = <<<'HTML'
<p>Last updated: February 11, 2025</p>
<p>This Privacy Policy describes Our policies and procedures on the collection, use and disclosure of Your information when You use the Service and tells You about Your privacy rights and how the law protects You.</p>
<h3>Interpretation and Definitions</h3>
<p><strong>Interpretation</strong><br>The words of which the initial letter is capitalized have meanings defined under the following conditions. The following definitions shall have the same meaning regardless of whether they appear in singular or in plural.</p>
<p><strong>Definitions</strong><br>For the purposes of this Privacy Policy:</p>
<ul>
<li><strong>Account</strong> means a unique account created for You to access our Service or parts of our Service.</li>
<li><strong>Company</strong> (referred to as either “the Company”, “We”, “Us” or “Our” in this Agreement) refers to <strong>TrustedU ERP</strong>, Trust Bank Tower, Gulshan, Dhaka-1212.</li>
<li><strong>Cookies</strong> are small files that are placed on Your computer, mobile device or any other device by a website...</li>
<li><strong>Country</strong> refers to: Bangladesh</li>
<li><strong>Device</strong> means any device that can access the Service such as a computer, a cellphone or a digital tablet.</li>
<li><strong>Personal Data</strong> is any information that relates to an identified or identifiable individual.</li>
<li><strong>Service</strong> refers to the Website.</li>
<li><strong>Service Provider</strong> refers to third-party companies or individuals employed by the Company to facilitate the Service...</li>
<li><strong>Usage Data</strong> refers to data collected automatically...</li>
<li><strong>Website</strong> refers to <strong>TrustedU ERP</strong>, accessible from <a href="https://trusteduerp.edu.bd">https://trusteduerp.edu.bd</a></li>
<li><strong>You</strong> means the individual accessing or using the Service...</li>
</ul>

<h3>Collecting and Using Your Personal Data</h3>
<p><strong>Types of Data Collected</strong><br><strong>Personal Data</strong><br>While using Our Service, We may ask You to provide Us with certain personally identifiable information that can be used to contact or identify You. Personally identifiable information may include, but is not limited to:<br>- Email address<br>- Usage Data</p>

<h3>Tracking Technologies and Cookies</h3>
<p>We use Cookies and similar tracking technologies to track the activity on Our Service and store certain information...</p>

<h3>Use of Your Personal Data</h3>
<p>The Company may use Personal Data for the following purposes:</p>
<ul>
<li>To provide and maintain our Service...</li>
<li>To manage Your Account...</li>
<li>For the performance of a contract...</li>
<li>To contact You...</li>
<li>To manage Your requests...</li>
</ul>

<h3>User and Authority Access to Personal Information</h3>
<p>The Service ensures that only the User and authorized authorities can access personal data. Users must go through a secure process to view their personal information. This process involves filling out the following details:</p>
<ul>
<li>Institute ID</li>
<li>User ID</li>
<li>Mobile number</li>
<li>Password</li>
</ul>

<h3>Security of Your Personal Data</h3>
<p>The security of Your Personal Data is important to Us, but remember that no method of transmission over the Internet, or method of electronic storage is 100% secure.</p>

<h3>Contact Us</h3>
<p>If you have any questions about this Privacy Policy, You can contact us:</p>
<ul>
<li>By email: <strong>info@tilbd.net</strong></li>
</ul>
HTML;

        Setting::updateOrCreate(
            ['key' => 'privacy_policy'],
            [
                'label' => 'Privacy Policy',
                'value' => $content,
                'group' => 'legal',
                'type' => 'richtext',
            ]
        );

        $this->info('Privacy policy seeded! -> info@tilbd.net / TrustedU ERP');
    }
}
