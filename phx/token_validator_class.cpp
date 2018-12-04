
#include "headers/token_validator_class.hpp"

token_validator::token_validator() {
}

token_validator::~token_validator() {
}

int
token_validator::validate() {
	return this->validate_header() && this->validate_token();
}

int
token_validator::validate_header() {
	Php::out << Php::GLOBALS["_SERVER"]["HTTP_AUTHORIZATION"] << std::string("\n");

	return 1;
}

int
token_validator::validate_token() {
	int r = 0;	
	r = this->validate();
	if (r) {
		r = this->validate_header();
		if (r) {
			return r;
		}
	}
	return 0;
}
