<?php

/**
 *  Copyright 2015 François Kooman <fkooman@tuxed.net>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace fkooman\OAuth\Test;

use fkooman\OAuth\ApprovalStorageInterface;
use fkooman\OAuth\Approval;

class TestApproval implements ApprovalStorageInterface
{
    public function storeApproval(Approval $approval)
    {
        return true;
    }

    public function isApproved(Approval $approval)
    {
        return false;
    }

    public function deleteApproval(Approval $approval)
    {
        return true;
    }

    public function getApprovalList($userId)
    {
        return array(
            new Approval(
                'user',
                'test-client',
                'https://example.org/cb',
                'code',
                'foo bar'
            ),
        );
    }
}
