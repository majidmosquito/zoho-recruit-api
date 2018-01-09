<?php

namespace Humantech\Zoho\Recruit\Api\Formatter;

use Humantech\Zoho\Recruit\Api\Formatter\Response\ErrorResponseFormatter;
use Humantech\Zoho\Recruit\Api\Formatter\Response\GenericResponseFormatter;
use Humantech\Zoho\Recruit\Api\Formatter\Response\GetFieldsResponseFormatter;
use Humantech\Zoho\Recruit\Api\Formatter\Response\GetModulesResponseFormatter;
use Humantech\Zoho\Recruit\Api\Formatter\Response\MessageResponseFormatter;
use Humantech\Zoho\Recruit\Api\Formatter\Response\DownloadFileResponseFormatter;
use Humantech\Zoho\Recruit\Api\Formatter\Response\NoDataResponseFormatter;

use Humantech\Zoho\Recruit\Api\Formatter\Response\AddRecordsResponseFormatter;

class ResponseFormatter extends AbstractFormatter implements FormatterInterface
{
    /**
     * @inheritdoc
     */
    public function formatter(array $data)
    {
        $this->originalData = $data;

        if (isset($data['download'])) {
            $this->setFormatter(new DownloadFileResponseFormatter());
        } elseif (isset($data['response']['nodata'])) {
            $this->setFormatter(new NoDataResponseFormatter());
        } elseif (isset($data['response']['result']['recorddetail']) 
            && in_array($this->getMethod(), [
                'addRecords', 'updateRecords'
            ]) ) {
            $this->setFormatter(new AddRecordsResponseFormatter );
        } elseif (isset($data['response']['result']['message']) || isset($data['response']['success']['message'])) {
            $this->setFormatter(new MessageResponseFormatter());
        } elseif (isset($data['response']['error'])) {
            $this->setFormatter(new ErrorResponseFormatter());
        } elseif ($this->isMethod('getFields')) {
            $this->setFormatter(new GetFieldsResponseFormatter());
        } elseif ($this->isMethod('getModules')) {
            $this->setFormatter(new GetModulesResponseFormatter());
        } elseif (in_array($this->getMethod(), array(
            'getRecords',
            'getRecordById',
            'getNoteTypes',
            'getRelatedRecords',
            'getAssociatedJobOpenings',
            'getAssociatedCandidates',
            'getSearchRecords',
            'searchRecords',
        ))) {
            $this->setFormatter(new GenericResponseFormatter());
        }

        if ($this->getFormatter() instanceof FormatterInterface) {
            $this->getFormatter()->formatter(array(
                'module' => $this->getModule(),
                'method' => $this->getMethod(),
                'data'   => $this->getOriginalData(),
                'params' => isset($data['params']) ? $data['params'] : null,
            ));
        }

        return $this;
    }
}
