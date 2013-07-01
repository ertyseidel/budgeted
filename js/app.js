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

//debug
$(document).keyup(function(evt){
	if(evt.keyCode == 71){ //'g'
		console.log(g);
	}
});

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

/** ----------------- ErrorMessage ----------------------- **/

var ErrorMessage = React.createClass({
	render: function() {
		return (
			<p class="error">{this.props.children}</p>
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
					g.changeState(STATE_MAIN_APP);
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
				{this.state.error == 'login_failure' ? <ErrorMessage>Incorrect Username or Password</ErrorMessage> : ''}
			</form>
		);
	}
});

/** ----------------- ErrorField ----------------------- **/
var ErrorField = React.createClass({
	render: function(){
		console.log("error field!");
	}
});



/** ----------------- RegisterForm ----------------------- **/

var RegisterForm = React.createClass({
	getInitialState: function(){
		return {errors: [], joinCreate: false, validUsername: -1, validOrg: -1}
	},
	setError: function(err, val){
		var e = this.state.errors;
		for(var i = e.length - 1; i >= 0; i--){
			if(e[i] == err){
				e.splice(i, 1);
				i = 0;
			}
		}
		if(val){
			e[e.length] = err;
		}
		return e;
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
						_this.setState({errors:this.setError('creation_falure', true)});
					}
				}
			},
			'json'
		);
		return false;
	}),
	joinToggleClick: React.autoBind(function(event){
		this.clearOrgState();
		var target = $(this.refs.form.getDOMNode());
		target.children('.createToggle').slideUp();
		target.children('.joinToggle').slideToggle();
	}),
	createToggleClick: React.autoBind(function(event){
		this.clearOrgState();
		var target = $(this.refs.form.getDOMNode());
		target.children('.joinToggle').slideUp();
		target.children('.createToggle').slideToggle();
	}),
	createAccountClick: React.autoBind(function(event){
		return true;
	}),
	usernameBlur: React.autoBind(function(event){
		var _this = this;
		if($(this.refs.username.getDOMNode()).val() == ''){
			_this.setState({errors: _this.setError('username_already_registered', false), validUsername: -1})
		} else{
			$.get('./ajax/checkusername.php',
				{name: $(event.nativeEvent.target).val()},
				function(data){
					if(data.status == 'failure'){
						_this.setState({errors: _this.setError('username_already_registered', true), validUsername: 0})
					} else{
						_this.setState({errors: _this.setError('username_already_registered', false), validUsername: 1})
					}
				},
				'json');
		}
		return false;
	}),
	orgCheck: React.autoBind(function(event){
		var _this = this;
		var target = $(event.target).prev();
		var val = target.val();
		if(val.length == 0){
			this.setState({validOrg: -1});
			return false;
		}
		if(target.attr("id") == this.refs.org_create_name.getDOMNode().getAttribute("id")){
			$.get('./ajax/checkorg.php',
				{org: val},
				function(data){
					if(data.status == 'success'){
						_this.setState({validOrg: 1, errors: _this.setError('bad_org_name', false)});
					} else{
						_this.setState({validOrg: 0, errors: _this.setError('bad_org_name', true)});
					}
				},
				'json'
			);
		} else if(target.attr("id") == this.refs.org_join_key.getDOMNode().getAttribute("id")){
			$.get('./ajax/checkorgkey.php',
				{key: val},
				function(data){
					if(data.status == 'success'){
						_this.setState({validOrg: 1, errors: _this.setError('bad_org_key', false)});
					} else{
						_this.setState({validOrg: 0, errors: _this.setError('bad_org_key', true)});
					}
				},
				'json'
			);
		}
		return false;
	}),
	clearOrgState: React.autoBind(function(event){
		if(this.state.validOrg != -1){
			this.setState({validOrg: -1, errors: this.setError('bad_org_key', false)});
		}
	}),
	render: function(){
		var e = this.state.errors.map(function(key){
			if(key == 'bad_org_name'){
				return <ErrorMessage>It seems that an organization by that name already exists!</ErrorMessage>;
			} else if(key == 'creation_failure'){
				return <ErrorMessage>Something went wrong during registration... Try again?</ErrorMessage>;
			} else if(key == 'username_already_registered'){
				return <ErrorMessage>That username is already taken, sorry.</ErrorMessage>;
			} else if(key == 'bad_org_key'){
				return <ErrorMessage>The key you have entered is not a valid key.</ErrorMessage>;
			}
		});
		return(
			<form onSubmit={this.handleSubmit} ref="form">
				<p>Username: <input type="text" ref="username" class={this.state.validUsername == -1 ? '' : this.state.validUsername == 1 ? 'goodInput' : 'badInput'} onBlur={this.usernameBlur}/></p>
				<p>Password: <input type="password" ref="password"/></p>
				<p><input type="button" onClick={this.joinToggleClick} value="Join an existing organization" /></p>
				<p class="joinToggle hidden">Organization Join Key: <input type="text" ref="org_join_key" class={this.state.validOrg == -1 ? '' : this.state.validOrg == 1 ? 'goodInput' : 'badInput'} onKeyUp={this.clearOrgState} /><input type="button" value="Check" onClick={this.orgCheck} /></p>
				<p class="joinToggle hidden">(Get this from someone already registered with the organization.)</p>
				<p><input type="button" onClick={this.createToggleClick} value="Create a new organization" /></p>
				<p class="createToggle hidden">Organization Name: <input type="text" ref="org_create_name" class={this.state.validOrg == -1 ? '' : this.state.validOrg == 1 ? 'goodInput' : 'badInput'} onKeyUp={this.clearOrgState} /> <input type="button" value="Check" onClick={this.orgCheck} /></p>
				{this.state.validOrg == 1  &&  this.state.validUsername == 1 ? <p><input type="submit" class="goodInput" value="Create Account" onClick={this.createAccountClick} /></p> : ''}
				{e}
			</form>
		);
	}
});