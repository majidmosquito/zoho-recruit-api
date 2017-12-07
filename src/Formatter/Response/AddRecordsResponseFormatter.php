<?php

namespace Humantech\Zoho\Recruit\Api\Formatter\Response;

use Humantech\Zoho\Recruit\Api\Formatter\FormatterInterface;

class AddRecordsResponseFormatter implements FormatterInterface
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var bool
     */
    private $isSingleResult;

    /**
     * @inheritdoc
     */
    public function formatter(array $data)
    {
        $result = $data['data']['response']['result'];
        $recorddetail = $result['recorddetail'];

        $FL = null;
        if(isset($recorddetail['FL'])){
        	$FL = $recorddetail['FL'];
        }
        $this->isSingleResult = false;

        if(!is_null($FL)){
        	$id = null;
        	foreach ($FL as $field) {
        		if($field['val'] == 'Id'){
        			$id = $field['content'];
        			break;
        		}
        	}
        	if(!is_null($id)){
				$this->data = $id;
        	}else{
        		$this->data = false;//$result['message'];
        	}
        }else{
        	$this->data = false;$result['message'];
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOutput()
    {
        return $this->isSingleResult === true ? array($this->data) : $this->data;
    }
}
