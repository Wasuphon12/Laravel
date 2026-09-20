<?php

namespace App\Services;

class PromptPayQrService
{
    /**
     * Generate a Thai QR Payment payload for a PromptPay mobile, national/tax, or e-wallet ID.
     * The payload is rendered as a QR image by the checkout page; no third-party payment API is used.
     */
    public function payload(?string $promptPayId, float $amount): ?string
    {
        $id = preg_replace('/\D/', '', (string) $promptPayId);
        if (! $id || $amount <= 0) {
            return null;
        }

        if (strlen($id) === 10 && str_starts_with($id, '0')) {
            $subTag = '01';
            $value = '0066'.substr($id, 1);
        } elseif (strlen($id) === 13) {
            $subTag = '02';
            $value = $id;
        } elseif (strlen($id) === 15) {
            $subTag = '03';
            $value = $id;
        } else {
            return null;
        }

        $merchantAccount = $this->tlv('00', 'A000000677010111').$this->tlv($subTag, $value);
        $payload = '000201010212'.$this->tlv('29', $merchantAccount)
            .'520400005303764'.$this->tlv('54', number_format($amount, 2, '.', ''))
            .'5802TH';

        return $payload.'6304'.$this->crc16($payload.'6304');
    }

    private function tlv(string $tag, string $value): string
    {
        return $tag.str_pad((string) strlen($value), 2, '0', STR_PAD_LEFT).$value;
    }

    private function crc16(string $payload): string
    {
        $crc = 0xFFFF;
        foreach (str_split($payload) as $character) {
            $crc ^= ord($character) << 8;
            for ($bit = 0; $bit < 8; $bit++) {
                $crc = ($crc & 0x8000) ? (($crc << 1) ^ 0x1021) : ($crc << 1);
                $crc &= 0xFFFF;
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }
}
