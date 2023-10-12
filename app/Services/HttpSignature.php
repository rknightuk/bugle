<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HttpSignature {

    public static function generateHeaders(
        $message,
        string $privateKey,
        string $host,
        string $path,
        string $keyId
    )
    {
        $hash = hash('sha256', json_encode($message), true);
        $digest = base64_encode($hash);

        $date = date('D, d M Y H:i:s \G\M\T');
        $signer = openssl_get_privatekey($privateKey);
        $stringToSign = "(request-target): post $path\nhost: $host\ndate: $date\ndigest: SHA-256=$digest";
        openssl_sign($stringToSign, $signature, $signer, OPENSSL_ALGO_SHA256);
        $signature_b64 = base64_encode($signature);

        $header = 'keyId="' . $keyId . '",algorithm="rsa-sha256",headers="(request-target) host date digest",signature="' . $signature_b64 . '"';

        return [
            'Host' => $host,
            'Date' => $date,
            'Signature' => $header,
            'Digest' => 'SHA-256=' . $digest,
            'Content-Type' => 'application/activity+json',
            'Accept' => 'application/activity+json',
        ];
    }

    public static function validateRequest(string $inboxPath, Request $request): bool
    {
        $values = [];
        $parts = explode(',', $request->header('signature'));

        foreach ($parts as $current) {
            $pair = explode('=', $current, 2);
            $key = $pair[0];
            $value = substr($pair[1], 1, -1);
            $values[$key] = $value;
        }

        $headerList = explode(' ', $values['headers']);
        $expectedHeaders = [];

        foreach ($headerList as $h) {
            if ($h === "(request-target)") {
                $expectedHeaders[] = "(request-target): post " . $inboxPath;
            } else {
                $expectedHeaders[] = $h . ": " . $request->headers->get($h);
            }
        }

        $signatureHeader = $request->header('signature');

        $signaturePairs = explode(',', $signatureHeader);
        $signatureHeaderMap = [];

        foreach ($signaturePairs as $pair) {
            $pairParts = explode('=', $pair);
            $key = trim($pairParts[0]);
            $value = trim($pairParts[1], '"');
            $signatureHeaderMap[$key] = $value;
        }

        $str = implode("\n", $expectedHeaders);

        $keyId = $signatureHeaderMap['keyId'];
        $actor = Http::accept('application/activity+json')->get($keyId);

        if (!isset($actor['publicKey']))
        {
            return false;
        }

        $publicKeyPem = $actor['publicKey']['publicKeyPem'];
        $verifier = openssl_get_publickey($publicKeyPem);

        $validate = openssl_verify($str, base64_decode($signatureHeaderMap['signature']), $verifier, OPENSSL_ALGO_SHA256);

        return $validate === 1;
    }

}
