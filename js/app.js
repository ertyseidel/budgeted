/** @jsx React.DOM */

var STATE_LOGIN = 0;
var STATE_REGISTER = 1;
var STATE_WELCOME = 4;
var STATE_MAIN_APP = 5;

var g = {}; //globals
g.user = false;
g.currentState = -1;
g.changeState = function(newState){
	switch(g.currentState){
		case STATE_LOGIN:
		case STATE_REGISTER:
			React.unmountAndReleaseReactRootNode(document.getElementById('absolute'));
			break;
		case STATE_MAIN_APP:
			console.log("Main App State Left!");
	}
	g.currentState = newState;
	switch(newState){
		case STATE_LOGIN:
			location.hash = "login";
			React.renderComponent(
				<PopUpBox>
					<LoginForm />
				</PopUpBox>,
				document.getElementById('absolute')
			);
			break;
		case STATE_REGISTER:
			location.hash = "register";
			React.renderComponent(
				<PopUpBox>
					<RegisterForm />
				</PopUpBox>,
				document.getElementById('absolute')
			);
			break;
		case STATE_MAIN_APP:
			location.hash = "app";
			console.log("Main App State Entered!");
	}
}

$(document).ready(function(){
	$.get('./ajax/checklogin.php',
		{},
		function(data){
			if(data.status == 'success'){
				g.user = JSON.parse(data.info);
				g.changeState(STATE_MAIN_APP);
			} else {
				g.changeState(STATE_LOGIN);
			}
		},
		'json'
	);
});

/** ----------------- PopUpBox ----------------------- **/

var PopUpBox = React.createClass({
	render: function() {
		return (
			<div class="popup-box-wrapper">
				<div class="popup-box">{this.props.children}</div>
			</div>
		);
	}
});

/** ----------------- LoginForm ----------------------- **/

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
					g.changeState(STATE_REGISTER);
				} else{
					if(data.info == 'login_failure'){
						_this.setState({error: 'login_failure'});
					}
				}
			},
			'json'
		);
		return false;
	}),
	registerButtonClickHandler: function(){
		g.changeState(STATE_REGISTER);
	},
	render: function(){
		return(
			<form onSubmit={this.handleSubmit}>
				<p>Username: <input type="text"  ref="username"/></p>
				<p>Password: <input type="password" ref="password"/></p>
				<input type="submit" value="Login" /> <input type="button" value="Register" onClick={this.registerButtonClickHandler}/>
				{this.state.error == 'login_failure' ? <p class="error">Incorrect Username or Password</p> : ''}
			</form>
		);
	}
});

/** ----------------- RegisterForm ----------------------- **/

var RegisterForm = React.createClass({
	getInitialState: function(){
		return {error: false, joinCreate: false, validUsername: -1, validOrg: -1}
	},
	handleSubmit: React.autoBind(function(event) {
		var username = this.refs.username.getDOMNode().value.trim();
		var password = this.refs.password.getDOMNode().value;
		if (username.length == 0 || password.length == 0) {
			return false;
		}
		var _this = this; //HAX
		$.post('./ajax/register.php',
			{
				'username': username,
				'password': password
			},
			function(data){
				if(data.status == 'success'){
					g.user = data.info;
					g.changeState(STATE_WELCOME);
				} else{
					if(data.info == 'creation_failure'){
						_this.setState({error: 'creation_falure'});
					}
				}
			},
			'json'
		);
		return false;
	}),
	joinToggleClick: React.autoBind(function(event){
		var target = $(this.refs.form.getDOMNode());
		console.log(target);
		target.children('.createToggle').slideUp();
		target.children('.joinToggle').slideToggle();
	}),
	createToggleClick: React.autoBind(function(event){
		var target = $(this.refs.form.getDOMNode());
		target.children('.joinToggle').slideUp();
		target.children('.createToggle').slideToggle();
	}),
	createAccountClick: React.autoBind(function(event){
		return true;
	}),
	usernameBlur: React.autoBind(function(event){
		var _this = this;
		$.get('./ajax/checkusername.php',
			{name: $(event.nativeEvent.target).val()},
			function(data){
				if(data.status == 'failure'){
					_this.setState({error: data.info, validUsername: 0})
				} else{
					_this.setState({error: false, validUsername: 1})
				}
			},
			'json');
		return false;
	}),
	orgBlur: React.autoBind(function(event){
		var _this = this;
		if(event.target == this.refs.org_create_name.getDOMNode()){
			this.setState({validOrg: 1});
		} else if(event.target == this.refs.org_join_key.getDOMNode()){
			this.setState({validOrg: 1});
		}
		return false;
	}),
	render: function(){
		return(
			<form onSubmit={this.handleSubmit} ref="form">
				<p>Username: <input type="text" ref="username" class={this.state.validUsername == -1 ? '' : this.state.validUsername == 1 ? 'goodInput' : 'badInput'} onBlur={this.usernameBlur}/></p>
				<p>Password: <input type="password" ref="password"/></p>
				<p><input type="button" onClick={this.joinToggleClick} value="Join an existing organization" /></p>
				<p class="joinToggle hidden">Organization Join Key: <input type="text" ref="org_join_key" onBlur={this.orgBlur}/></p>
				<p class="joinToggle hidden">(Get this from someone already registered with the organization.)</p>
				<p><input type="button" onClick={this.createToggleClick} value="Create a new organization" /></p>
				<p class="createToggle hidden">Organization Name: <input type="text" ref="org_create_name" onBlur={this.orgBlur}/></p>
				{this.state.validOrg == 1 ? <p><input type="submit" value="Create Account" onClick={this.createAccountClick} /></p> : ''}
				{this.state.error == 'bad_org_name'? <p class="error">You need to have an organization to join!</p> : ''}
				{this.state.error == 'creation_failure' ? <p class="error">Something went wrong during registration... Try again?</p> : ''}
				{this.state.error == 'username_already_registered' ? <p class="error">That username is already taken, sorry.</p> : ''}
			</form>
		);
	}
});