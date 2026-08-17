<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terms and Conditions | {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl w-full space-y-8 bg-white p-10 rounded-xl shadow-lg border border-gray-100">
            <div class="text-center border-b border-gray-200 pb-8">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Terms and Conditions</h1>
                <p class="mt-2 text-sm text-gray-500">Please read these terms carefully before using our services.</p>
            </div>

            <div class="space-y-6 text-sm leading-relaxed">
                <!-- Introduction -->
                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-md">
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-amber-700 font-medium">
                                সঠিকভাবে সম্পূর্ণ শর্তাবলী পড়ুন। অন্যথায় কোনো কারণে আপনার অ্যাকাউন্ট সাসপেন্ড হলে কোনো
                                ধরনের আপত্তি বা অনুরোধ গ্রহণযোগ্য হবে না।
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Terms List -->
                <div class="space-y-6">
                    <!-- Item 1 -->
                    <div class="group">
                        <h3 class="text-gray-900 font-semibold mb-1">Data Retention</h3>
                        <p class="text-gray-600">Your sent SMS will not be stored on our servers for a long time.</p>
                        <p class="text-gray-500 italic mt-1 text-xs">(আপনার পাঠানো এসএমএস আমাদের সার্ভারে দীর্ঘ সময়
                            সংরক্ষণ করা হয় না।)</p>
                    </div>

                    <!-- Item 2 -->
                    <div class="group border-t border-gray-100 pt-4">
                        <h3 class="text-gray-900 font-semibold mb-1">Package Validity & Rollover</h3>
                        <p class="text-gray-600">If the new SMS package is recharged before the old SMS package expires,
                            the remaining SMS will be added to the new package. But after the expiration, the old SMS
                            will not be added.</p>
                        <p class="text-gray-500 italic mt-1 text-xs">(পুরাতন এসএমএস প্যাকেজের মেয়াদ শেষ হওয়ার আগে নতুন
                            প্যাকেজ রিচার্জ করলে অবশিষ্ট এসএমএস নতুন প্যাকেজের সাথে যুক্ত হবে। মেয়াদ শেষ হওয়ার পরে
                            রিচার্জ করলে পুরাতন এসএমএস যোগ হবে না।)</p>
                    </div>

                    <!-- Item 3 -->
                    <div class="group border-t border-gray-100 pt-4">
                        <h3 class="text-red-600 font-semibold mb-1">Prohibited Content: Abuse & Illegal Activities</h3>
                        <p class="text-gray-600">Message cannot be used for any abuse, threats, or anti-national
                            activities. Any illegal use is strictly prohibited. Legal action will be taken and the
                            account will be closed.</p>
                        <p class="text-gray-500 italic mt-1 text-xs">(গালিগালাজ, হুমকি বা দেশবিরোধী কাজে এসএমএস পাঠানো
                            সম্পূর্ণ নিষিদ্ধ। বেআইনি কাজে ব্যবহার করলে আইনানুগ ব্যবস্থা নেওয়া হবে এবং অ্যাকাউন্ট বন্ধ
                            করা হবে।)</p>
                    </div>

                    <!-- Item 4 -->
                    <div class="group border-t border-gray-100 pt-4">
                        <h3 class="text-red-600 font-semibold mb-1">Prohibited Content: Personal & Inappropriate</h3>
                        <p class="text-gray-600">Personal SMS such as threat, love, slang, adult, or any kind of
                            personal message is strictly prohibited. Violation will result in permanent account
                            suspension without any request acceptance.</p>
                        <p class="text-gray-500 italic mt-1 text-xs">(ব্যক্তিগত ব্যবহার যেমন ভালোবাসা, অ্যাডাল্ট বা
                            অশালীন এসএমএস পাঠানো যাবে না। করলে সাথে সাথে অ্যাকাউন্ট স্থায়ীভাবে সাসপেন্ড করা হবে এবং
                            কোনো রিকোয়েস্ট গ্রহণ করা হবে না।)</p>
                    </div>

                    <!-- Item 5 -->
                    <div class="group border-t border-gray-100 pt-4">
                        <h3 class="text-gray-900 font-semibold mb-1">Fraudulent Activities</h3>
                        <p class="text-gray-600">No fraudulent or misleading promotional SMS is allowed (e.g. lottery
                            winning messages).</p>
                        <p class="text-gray-500 italic mt-1 text-xs">(কোনো ধরনের প্রতারণামূলক বা বিভ্রান্তিকর প্রচারণা
                            এসএমএস পাঠানো যাবে না।)</p>
                    </div>

                    <!-- Item 6 -->
                    <div class="group border-t border-gray-100 pt-4">
                        <h3 class="text-gray-900 font-semibold mb-1">Restricted Marketing</h3>
                        <p class="text-gray-600">No adult product marketing is allowed.</p>
                        <p class="text-gray-500 italic mt-1 text-xs">(কোনো অ্যাডাল্ট প্রোডাক্টের মার্কেটিং করা নিষিদ্ধ।)
                        </p>
                    </div>

                    <!-- Item 7 -->
                    <div class="group border-t border-gray-100 pt-4">
                        <h3 class="text-gray-900 font-semibold mb-1">Service Speed</h3>
                        <p class="text-gray-600">SMS sending speed is instant, but for large volume it may take some
                            time. Please be patient.</p>
                    </div>

                    <!-- Item 8 -->
                    <div class="group border-t border-gray-100 pt-4">
                        <h3 class="text-red-600 font-semibold mb-1">Liability & Responsibility</h3>
                        <p class="text-gray-600">You are fully responsible for all SMS sent from your account.
                            <strong>SOFTCENTS VOICE SMS</strong> will not be liable for any misuse. Legal action will be
                            taken for any illegal activity and the account will be permanently closed.
                        </p>
                        <p class="text-gray-500 italic mt-1 text-xs">(আপনার অ্যাকাউন্ট থেকে পাঠানো সকল এসএমএসের সম্পূর্ণ
                            দায়ভার আপনার। এর জন্য SOFTCENTS VOICE SMS কোনোভাবেই দায়ী থাকবে না।)</p>
                    </div>

                    <!-- Item 9 -->
                    <div class="group border-t border-gray-100 pt-4">
                        <h3 class="text-red-600 font-semibold mb-1">Refund Policy & Monitoring</h3>
                        <div class="bg-red-50 p-4 rounded-md border-l-4 border-red-500">
                            <p class="text-red-800 font-medium">অ্যাকাউন্ট সাসপেন্ড হলে কোনো ধরনের টাকা ফেরত দেওয়া হবে
                                না। আমরা প্রতিটি এসএমএস মনিটর করি এবং যেকোনো বেআইনি বা খারাপ উদ্দেশ্যে ব্যবহারের
                                ক্ষেত্রে অ্যাকাউন্ট সাসপেন্ডসহ আইনানুগ ব্যবস্থা গ্রহণ করা হবে।</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 mt-8 pt-6 text-center">
                    <p class="text-gray-400 text-xs">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights
                        reserved.</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>