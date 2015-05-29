<?php

class Controller_api_credit_add extends Crunchbutton_Controller_RestAccount {

	public function init() {

		$this->_permission();

		switch ( $this->method() ) {
			case 'get':
				$this->_error();
			break;
			case 'post':
				$this->_post();
			break;
		}
	}

	private function _post(){
		$this->_save();
	}

	private function _save(){

		$id_user = $this->request()[ 'id_user' ];
		$value = $this->request()[ 'value' ];
		$note = $this->request()[ 'note' ];

		if( !$id_user ){
			$this->_error( 'It seems you have not selected an user!' );
		}

		if( !$value ){
			$this->_error( 'Please enter a value!' );
		}

		if( !$note ){
			$this->_error( 'Please enter a note!' );
		}

		$giftcard = new Crunchbutton_Promo;
		$giftcard->note = $note;
		$giftcard->value = $value;
		$giftcard->id_user = $id_user;
		$giftcard->type = Crunchbutton_Promo::TYPE_GIFTCARD;
		$giftcard->active = 1;
		$giftcard->id_admin = c::user()->id_admin;
		$giftcard->date = date('Y-m-d H:i:s');
		$giftcard->code = $giftcard->promoCodeGeneratorUseChars( Crunchbutton_Promo::CHARS, 10, '', '.la.' );
		$giftcard->issued = Crunchbutton_Promo::ISSUED_CREDIT;
		$giftcard->save();

		$credit = $giftcard->addCredit( $id_user, 0, $note );

		if( $credit->id_credit ){
			$message = '$' . $credit->value . ' credit added to ' . $credit->user()->name . '!';
			echo json_encode( [ 'success' => $message ] );
			exit();
		} else {
			$this->_error();
		}
	}

	private function _error( $error = 'invalid request' ){
		echo json_encode( [ 'error' => $error ] );
		exit();
	}

	private function _permission(){
		if (!c::admin()->permission()->check(['global', 'gift-card-all', 'gift-card-create-all', 'support-all', 'support-view', 'support-crud' ])) {
			header('HTTP/1.1 401 Unauthorized');
			exit;
		}
	}


}