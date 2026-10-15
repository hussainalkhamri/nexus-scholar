<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REQ-{{ 3000 + $doc->id }} - {{ $doc->doc_type_en }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&family=Outfit:wght@400;600;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailored styling for printable certificate -->
    <style>
        body {
            font-family: 'Cairo', 'Outfit', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 40px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .no-print-bar {
            width: 100%;
            max-width: 800px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .btn-print {
            background-color: #1e3a8a;
            color: #ffffff;
            font-size: 14px;
            font-weight: bold;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(30, 58, 138, 0.2);
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-print:hover {
            background-color: #1a337a;
            transform: translateY(-1px);
        }

        .btn-back {
            color: #4b5563;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }

        /* Certificate page frame */
        .certificate-container {
            width: 800px;
            height: 1130px; /* A4 aspect ratio height */
            background-color: #ffffff;
            border: 24px double #1e3a8a;
            padding: 40px;
            box-sizing: border-box;
            position: relative;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .cert-inner-border {
            border: 2px solid #b45309;
            height: 100%;
            width: 100%;
            padding: 30px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        /* Header block */
        .cert-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px double #1e3a8a;
            padding-bottom: 20px;
        }

        .cert-header-side {
            width: 35%;
            font-size: 11px;
            font-weight: bold;
            color: #1f2937;
            line-height: 1.6;
        }

        .cert-header-side.ar {
            text-align: right;
        }

        .cert-header-side.en {
            text-align: left;
        }

        .cert-logo {
            width: 80px;
            height: auto;
        }

        /* Document Title */
        .cert-title-container {
            text-align: center;
            margin: 40px 0 20px 0;
        }

        .cert-title-ar {
            font-size: 32px;
            font-weight: 900;
            color: #1e3a8a;
            margin: 0;
        }

        .cert-title-en {
            font-size: 24px;
            font-weight: 800;
            color: #b45309;
            margin: 5px 0 0 0;
            letter-spacing: 1px;
        }

        /* Main details layout */
        .cert-body {
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 40px;
        }

        .cert-text {
            font-size: 14px;
            color: #374151;
            line-height: 2;
            margin-bottom: 25px;
        }

        .cert-text-en {
            font-size: 13px;
            color: #4b5563;
            line-height: 1.8;
            margin-bottom: 35px;
            font-style: italic;
        }

        .highlight-name {
            font-size: 22px;
            font-weight: 900;
            color: #111827;
            display: block;
            margin: 10px 0;
        }

        .highlight-detail {
            font-weight: bold;
            color: #1e3a8a;
        }

        /* Footer Stamp and Signatures */
        .cert-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 30px;
        }

        .footer-sign-block {
            text-align: center;
            width: 30%;
        }

        .sign-title-ar {
            font-size: 12px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 40px;
        }

        .sign-title-en {
            font-size: 10px;
            color: #4b5563;
            margin-top: 5px;
        }

        .signature-mockup {
            font-family: cursive;
            font-size: 16px;
            color: #1e3a8a;
            opacity: 0.8;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 5px;
            display: inline-block;
            width: 120px;
        }

        /* Verification seal / QR code center */
        .verification-seal-block {
            text-align: center;
            width: 35%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .qr-image {
            width: 100px;
            height: 100px;
            background-color: #f3f4f6;
            border: 2px solid #1e3a8a;
            padding: 5px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .verification-text {
            font-size: 9px;
            color: #6b7280;
            margin-top: 8px;
            font-weight: bold;
            line-height: 1.4;
            max-width: 160px;
        }

        /* Background Watermark */
        .cert-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 450px;
            height: auto;
            opacity: 0.035;
            pointer-events: none;
            z-index: 0;
        }

        /* Print media rules */
        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
                margin: 0;
            }

            .no-print-bar {
                display: none;
            }

            .certificate-container {
                box-shadow: none;
                border: 24px double #1e3a8a;
                margin: 0;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>

    <!-- Top floating bar (hidden on print) -->
    <div class="no-print-bar">
        <a href="{{ route('alumni.requests.index') }}" class="btn-back">
            &larr; العودة إلى الطلبات
        </a>
        <button onclick="window.print()" class="btn-print">
            <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>طباعة / حفظ كملف PDF</span>
        </button>
    </div>

    <!-- Official Certificate Container -->
    <div class="certificate-container">
        <!-- Watermark background seal -->
        <svg class="cert-watermark" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM13 16h-2v2h2v-2zm0-6h-2v4h2v-4z"/>
        </svg>

        <div class="cert-inner-border">
            <!-- Header Block -->
            <div class="cert-header">
                <div class="cert-header-side ar">
                    جامعة نكسوس للعلوم والتكنولوجيا<br>
                    عمادة شؤون الطلاب والخريجين<br>
                    مسجل عام الكلية
                </div>
                <!-- Logo Image -->
                <img class="cert-logo" src="{{ asset('logo.svg') }}" alt="Logo" style="border-radius: 8px; max-height: 80px; object-fit: contain;">

                <div class="cert-header-side en">
                    Nexus University of Science & Technology<br>
                    Deanship of Student Affairs<br>
                    Office of the Registrar
                </div>
            </div>

            <!-- Titles -->
            <div class="cert-title-container">
                <h1 class="cert-title-ar">{{ $doc->doc_type_ar }}</h1>
                <h2 class="cert-title-en">{{ $doc->doc_type_en }}</h2>
            </div>

            <!-- Body Details -->
            <div class="cert-body">
                <div class="cert-text">
                    تشهد جامعة نكسوس للعلوم والتكنولوجيا بأن الطالب / الطالبة:<br>
                    <span class="highlight-name">{{ app()->getLocale() == 'ar' ? $doc->alumnus->name_ar : $doc->alumnus->name_en }}</span>
                    قد أكمل بنجاح متطلبات التخرج ونال شهادة البكالوريوس في قسم <span class="highlight-detail">{{ $doc->alumnus->department }}</span><br>
                    بمعدل تراكمي ممتاز وتخرج للعام الدراسي الجامعي <span class="highlight-detail">{{ $doc->alumnus->alumniProfile->graduation_year ?? date('Y') }}</span>.
                </div>

                <div class="cert-text-en">
                    This is to certify that:<br>
                    <span class="highlight-name" style="font-family: 'Outfit'; font-weight: 800;">{{ $doc->alumnus->name_en }}</span>
                    has successfully fulfilled the graduation requirements and is hereby awarded the Bachelor Degree in<br>
                    <span class="highlight-detail" style="font-family: 'Outfit';">{{ $doc->alumnus->department }}</span> 
                    for the academic year <span class="highlight-detail" style="font-family: 'Outfit';">{{ $doc->alumnus->alumniProfile->graduation_year ?? date('Y') }}</span>.
                </div>
            </div>

            <!-- Footer Signatures & QR Seal -->
            <div class="cert-footer">
                <!-- Signature 1 -->
                <div class="footer-sign-block">
                    <div class="sign-title-ar">عميد الكلية الأكاديمي</div>
                    <div class="signature-mockup">Dean Academic</div>
                    <div class="sign-title-en">Dean of the Faculty</div>
                </div>

                <!-- QR Center Seal -->
                <div class="verification-seal-block">
                    <!-- Real QR Code Generator API -->
                    <img class="qr-image" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('documents.verify', $doc->verification_code)) }}" alt="QR Verification Seal">
                    <div class="verification-text">
                        تحقق من صحة الوثيقة برمز الاستجابة<br>
                        Verify authenticity via this secure QR Code.<br>
                        Verification: REQ-{{ 3000 + $doc->id }}
                    </div>
                </div>

                <!-- Signature 2 -->
                <div class="footer-sign-block">
                    <div class="sign-title-ar">مسجل عـــام الجامعة</div>
                    <div class="signature-mockup">Registrar Gen.</div>
                    <div class="sign-title-en">University Registrar</div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
