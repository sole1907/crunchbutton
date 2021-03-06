NGApp.controller( 'CreditDialogCtrl', function ( $scope, $rootScope, CustomerService, CreditService ) {


	$rootScope.$on('creditDialog', function(e, id_user) {

		$scope.isLoading = true;

		$(':focus').blur();

		$scope.credit = { add_as_credit: true };

		$scope.formCreditSubmitted = false;
		$scope.isSaving = false;

		CustomerService.get( id_user, function( json ){
			$scope.credit.id_user = json.id_user;
			$scope.credit.name = json.name;
			App.dialog.show('.credit-dialog');

			$scope.isLoading = false;

		} );

		$scope.$watch( 'credit.add_as_credit', function( newValue, oldValue, scope ) {
			if( !newValue ){
				$scope.credit.send_notification = true;
			}
		});

		$scope.$watch( 'credit.send_notification', function( newValue, oldValue, scope ) {
			if( !newValue ){
				$scope.credit.add_as_credit = true;
			}
		});

		$scope.complete = $rootScope.closePopup;

	});

	$scope.save = function(){

		if( $scope.isSaving ){
			return;
		}

		if( $scope.formCredit.$invalid ){
			$scope.formCreditSubmitted = true;
			$scope.isSaving = false;
			return;
		}

		$scope.isSaving = true;

		CreditService.add( $scope.credit, function( json ){
			if( json.success ){
				$rootScope.$broadcast( 'creditAdded' );
				$rootScope.closePopup();
				setTimeout( function(){ App.alert( json.success + '<br>' ); }, 500 );
			} else {
				$rootScope.closePopup();
				setTimeout(function(){App.alert( 'Error: ' + json.error , 'error' );}, 500);
			}
			$scope.isSaving = false;
		} );
	}
});