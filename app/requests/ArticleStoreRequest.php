<?php
    // app/requests/UserStoreRequest.php
    class UserStoreRequest extends BaseRequest {
        public function rules() {
            return [
                'title'   => 'required|min:2|max:100'
            ];
        }
    }