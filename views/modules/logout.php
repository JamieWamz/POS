<?php

require_method('POST');

session_destroy();

echo '<script>

	window.location = "login";

</script>';
