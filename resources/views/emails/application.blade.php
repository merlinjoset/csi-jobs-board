@php
    $job = $application->jobPost;
    $seeker = $application->seeker;
    $company = $job->company;
    $appUrl = config('app.url');
@endphp
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width"></head>
<body style="margin:0;background:#f5f5f4;font-family:'Segoe UI',Arial,sans-serif;color:#292524;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f4;padding:24px 0;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e7e5e4;">
                {{-- Header --}}
                <tr><td style="background:linear-gradient(135deg,#059669,#065f46);padding:22px 28px;">
                    <span style="color:#ffffff;font-size:18px;font-weight:800;letter-spacing:-.2px;">CSI Job Portal</span>
                    <span style="color:#d1fae5;font-size:12px;"> &nbsp;Jobs for our community</span>
                </td></tr>

                <tr><td style="padding:28px;">
                    @if ($event === 'received')
                        <h1 style="margin:0 0 6px;font-size:20px;color:#111827;">New applicant for your job</h1>
                        <p style="margin:0 0 16px;color:#57534e;font-size:14px;line-height:1.6;">
                            <strong>{{ $seeker->name }}</strong> has applied to <strong>{{ $job->title }}</strong> at {{ $company }}.
                        </p>
                        @php $cta = $appUrl.'/provider/jobs/'.$job->id.'/applicants'; $ctaLabel = 'Review applicant'; @endphp
                    @elseif ($event === 'shortlisted')
                        <h1 style="margin:0 0 6px;font-size:20px;color:#047857;">Good news, you have been shortlisted!</h1>
                        <p style="margin:0 0 16px;color:#57534e;font-size:14px;line-height:1.6;">
                            Hi {{ $seeker->name }}, {{ $company }} has shortlisted your application for <strong>{{ $job->title }}</strong>. They may reach out with interview details soon.
                        </p>
                        @php $cta = $appUrl.'/my-applications'; $ctaLabel = 'View my applications'; @endphp
                    @elseif ($event === 'interview')
                        <h1 style="margin:0 0 6px;font-size:20px;color:#047857;">You are invited to an interview</h1>
                        <p style="margin:0 0 14px;color:#57534e;font-size:14px;line-height:1.6;">
                            Hi {{ $seeker->name }}, {{ $company }} would like to interview you for <strong>{{ $job->title }}</strong>.
                        </p>
                        <table role="presentation" width="100%" style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:12px;margin:0 0 16px;">
                            <tr><td style="padding:14px 16px;font-size:14px;color:#374151;line-height:1.7;">
                                <strong>When:</strong> {{ optional($application->interview_at)->format('l, d M Y \a\t g:i A') }}<br>
                                <strong>Mode:</strong> {{ $application->interview_mode }}<br>
                                @if ($application->interview_location)<strong>Where:</strong> {{ $application->interview_location }}<br>@endif
                                @if ($application->interview_note)<strong>Note:</strong> {{ $application->interview_note }}@endif
                            </td></tr>
                        </table>
                        @php $cta = $appUrl.'/my-applications'; $ctaLabel = 'View interview details'; @endphp
                    @else
                        <h1 style="margin:0 0 6px;font-size:20px;color:#111827;">Update on your application</h1>
                        <p style="margin:0 0 16px;color:#57534e;font-size:14px;line-height:1.6;">
                            Hi {{ $seeker->name }}, there is an update on your application for <strong>{{ $job->title }}</strong> at {{ $company }}. Thank you for applying, and we encourage you to keep exploring other roles.
                        </p>
                        @php $cta = $appUrl.'/my-applications'; $ctaLabel = 'View my applications'; @endphp
                    @endif

                    @if (!empty($application->provider_message) && $event !== 'received')
                        <table role="presentation" width="100%" style="background:#f5f5f4;border-radius:10px;margin:0 0 16px;">
                            <tr><td style="padding:12px 14px;font-size:13px;color:#44403c;">
                                <span style="color:#78716c;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Message from {{ $company }}</span><br>
                                {{ $application->provider_message }}
                            </td></tr>
                        </table>
                    @endif

                    <a href="{{ $cta }}" style="display:inline-block;background:#059669;color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:11px 20px;border-radius:10px;">{{ $ctaLabel }}</a>
                </td></tr>

                <tr><td style="padding:16px 28px;border-top:1px solid #f0efed;color:#a8a29e;font-size:12px;">
                    CSI Job Portal &middot; Jobs for our community
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
