/** @jsx React.DOM */
var LoginForm = React.createClass({
	getInitialState: function(){
		return {error: false}
	},
	handleSubmit: React.autoBind(function(event) {
		var username = this.refs.username.getDOMNode().value.trim();
		var password = this.refs.password.getDOMNode().value;
		if (username.length == 0 || password.length == 0) {
			return false;
		}
		var _this = this; //HAX
		$.post('./ajax/login.php',
			{
				'username': username,
				'password': password
			},
			function(data){
				if(data.status == 'success'){
					g.user = data.info;
					g.setState(STATE_REGISTER);
				} else{
					if(data.info == 'login_failure'){
						_this.setState({error: true});
					}
				}
			},
			'json'
		);
		return false;
	}),
	getErrorText: function(){
		return this.state.error ? <p class="error">Incorrect Username or Password</p> : '';
	},
	render: function(){
		return(
			<form onSubmit={this.handleSubmit}>
				<p>Username: <input type="text"  ref="username"/></p>
				<p>Password: <input type="password" ref="password"/></p>
				<input type="submit" value="Login" />
				{this.getErrorText()}
			</form>
		);
	}
});