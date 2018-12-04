
#include "headers/token_validator.hpp"
#include "headers/token_validator_class.hpp"

Php::Value
validate_token(Php::Parameters &p) {
	token_validator *validator = new token_validator(p[0]);
	int r = validator->validate();
	free(validator);
	validator = nullptr;
	return r;
}
