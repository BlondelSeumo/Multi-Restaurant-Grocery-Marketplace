<?php

namespace App\Services\EmailTemplateService;

use App\Events\Mails\EmailSendByTemplate;
use App\Helpers\ResponseError;
use App\Models\EmailTemplate;
use App\Services\CoreService;
use DB;
use Throwable;

class EmailTemplateService extends CoreService
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return EmailTemplate::class;
    }

    public function create(array $data): array
    {
        try {
            $verify         = EmailTemplate::TYPE_VERIFY;

            if (
                $data['type'] === $verify && (
                    !stristr($data['body'], '$verify_code') ||
                    !stristr($data['alt_body'], '$verify_code')
                )
            ) {
                return [
                    'status'  => false,
                    'message' => __('errors.' . ResponseError::ERROR_112, ['verify' => $verify], $this->language),
                    'code'    => ResponseError::ERROR_400
                ];
            }

            /** @var EmailTemplate $emailTemplate */
            $emailTemplate = DB::transaction(function () use ($verify) {
                $data['status'] = 0;

                if ($data['type'] === $verify) {
                    $this->model()->where('type', $verify)->delete();
                }

                return $this->model()->create($data);
            });

            if (
                date('Y-m-d H', strtotime($emailTemplate->send_to)) === date('Y-m-d H') &&
                $emailTemplate->type == EmailTemplate::TYPE_SUBSCRIBE
            ) {
                event((new EmailSendByTemplate(EmailTemplate::find($emailTemplate->id))));
            }

            return [
                'status'    => true,
                'code'      => ResponseError::NO_ERROR,
            ];
        } catch (Throwable $e) {

            $this->error($e);

            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_502,
                'message' => __('errors.' . ResponseError::ERROR_502, locale: $this->language)
            ];
        }
    }

    public function update(EmailTemplate $emailTemplate, array $data): array
    {
        try {
            $data['status'] = 0;
            $verify         = EmailTemplate::TYPE_VERIFY;

            if (
                $data['type'] === $verify && (
                    !stristr($data['body'], '$verify_code') ||
                    !stristr($data['alt_body'], '$verify_code')
                )
            ) {
                return [
                    'status'  => false,
                    'message' => __('errors.' . ResponseError::ERROR_112, ['verify' => $verify], $this->language),
                    'code'    => ResponseError::ERROR_400
                ];
            }

            $emailTemplate->update($data);

            if (
                date('Y-m-d H', strtotime($emailTemplate->send_to)) === date('Y-m-d H') &&
                $emailTemplate->type == EmailTemplate::TYPE_SUBSCRIBE
            ) {
                event((new EmailSendByTemplate(EmailTemplate::find($emailTemplate->id))));
            }

            return [
                'status'    => true,
                'code'      => ResponseError::NO_ERROR,
            ];
        } catch (Throwable $e) {

            $this->error($e);

            return [
                'status'    => false,
                'code'      => ResponseError::ERROR_501,
            ];
        }
    }

    public function delete(?array $ids = []): void
    {
        foreach ($this->model()->whereIn('id', is_array($ids) ? $ids : [])->get() as $emailTemplate) {
            $emailTemplate->delete();
        }
    }
}
