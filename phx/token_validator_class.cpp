
#include "headers/token_validator_class.hpp"

token_validator::token_validator(std::string token) {
	this->token = token;
}

token_validator::~token_validator() {
}

int
token_validator::validate() {
	return this->validate_header() && this->validate_token();
}

int
token_validator::validate_header() {
	

	return 1;
}

int
token_validator::validate_token() {
	

	return 1;
}
