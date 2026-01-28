<script setup lang="ts">
import FormGroup from "Components/Form/FormGroup.vue";
</script>

<template>
    <form method="POST" action="/auth/register">
        <form-group for-id="name" label="Benutzername"><input type="text" name="name" id="name" /></form-group>
        <form-group for-id="email" label="E-Mail-Adresse"><input type="email" name="email" id="email" /></form-group>
        <form-group for-id="password" label="Passwort"
            ><input type="password" name="password" id="password"
        /></form-group>
        <form-group>
            <button type="submit" class="btn-primary">Submit!</button>
            <button type="submit" class="btn-default">Reset</button>
        </form-group>
    </form>
</template>
