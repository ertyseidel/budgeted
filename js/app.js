/** @jsx React.DOM */

var g; //globals
g.user = false;

var PopUpBox = React.createClass({
	render: function() {
		return (
			<div class="popup-box-wrapper">
				<div class="popup-box">{this.props.children}</div>
			</div>
		);
	}
});

var LoginForm = React.createClass({
	getInitialState: function(){
		return {}
	},
	handleSubmit: React.autoBind(function(event) {
		var username = this.refs.username.getDOMNode().value.trim();
		var password = this.refs.password.getDOMNode().value;
		if (username.length < 3 || password.length < 3) {
			return false;
		}
		$.post('./ajax/login.php',
			{
				'username': username,
				'password': password
			},
			function(data){
				if(data.status == 'success'){
					g.user = data.info;
				} else{

				}
			},
			'json'
		);
		return false;
	}),
	render: function(){
		return(
			<form onSubmit={this.handleSubmit}>
				<p>Username: <input type="text"  ref="username"/></p>
				<p>Password: <input type="password" ref="password"/></p>
				<input type="submit" value="Login" />
			</form>
		);
	}
});

React.renderComponent(
	<PopUpBox>
		<LoginForm />
	</PopUpBox>,
	document.getElementById('absolute')
);