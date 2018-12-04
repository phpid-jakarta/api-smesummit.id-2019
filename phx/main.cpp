
#include "headers/token_validator.hpp"

extern "C" {
	PHPCPP_EXPORT void *get_module() {
		static Php::Extension extension("apismesummit_ext1", "1.0");
		extension.add<validate_token>("validate_token", {
			Php::ByVal("token", Php::Type::String)
		});
		return extension;
	}
}
