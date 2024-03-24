module.exports = {
    /** Pressing the "fly up" key when using touch. */
    ASCEND: 0,
    /** Pressing the "fly down" key when using touch. */
    DESCEND: 1,
    /** Pressing (and optionally holding) the jump key (while not flying). */
    NORTH_JUMP: 2,
    /** Pressing (and optionally holding) the jump key (including while flying). */
    JUMP_DOWN: 3,
    /** Pressing (and optionally holding) the sprint key (typically the CTRL key). Does not include double-pressing the forward key. */
    SPRINT_DOWN: 4,
    /** Pressing (and optionally holding) the fly button ONCE when in flight mode when using touch. This has no obvious use. */
    CHANGE_HEIGHT: 5,
    /** Pressing (and optionally holding) the jump key (including while flying), and also auto-jumping. */
    JUMPING: 6,
    /** Auto-swimming upwards while pressing forwards with auto-jump enabled. */
    AUTO_JUMPING_IN_WATER: 7,
    /** Sneaking, and pressing the "fly down" key or "sneak" key (including while flying). */
    SNEAKING: 8,
    /** Pressing (and optionally holding) the sneak key (including while flying). This includes when the sneak button is toggled ON with touch controls. */
    SNEAK_DOWN: 9,
    /** Pressing the forward key (typically W on keyboard). */
    UP: 10,
    /** Pressing the backward key (typically S on keyboard). */
    DOWN: 11,
    /** Pressing the left key (typically A on keyboard). */
    LEFT: 12,
    /** Pressing the right key (typically D on keyboard). */
    RIGHT: 13,
    /** Pressing the ↖ key on touch. */
    UP_LEFT: 14,
    /** Pressing the ↗ key on touch. */
    UP_RIGHT: 15,
    /** Client wants to go upwards. Sent when Ascend or Jump is pressed, irrespective of whether flight is enabled. */
    WANT_UP: 16,
    /** Client wants to go downwards. Sent when Descend or Sneak is pressed, irrespective of whether flight is enabled. */
    WANT_DOWN: 17,
    /** Same as "want up" but slow. Only usable with controllers at the time of writing. Triggered by pressing the right joystick by default. */
    WANT_DOWN_SLOW: 18,
    /** Same as "want down" but slow. Only usable with controllers at the time of writing. Not bound to any control by default. */
    WANT_UP_SLOW: 19,
    /** Unclear usage, during testing it was only seen in conjunction with SPRINT_DOWN. NOT sent while actually sprinting. */
    SPRINTING: 20,
    /** Ascending scaffolding. Note that this is NOT sent when climbing ladders. */
    ASCEND_BLOCK: 21,
    /** Descending scaffolding. */
    DESCEND_BLOCK: 22,
    /** Toggling the sneak button on touch when the button enters the "enabled" state. */
    SNEAK_TOGGLE_DOWN: 23,
    /** Unclear use. Sent continually on touch controls, irrespective of whether the player is actually sneaking or not. */
    PERSIST_SNEAK: 24,
    START_SPRINTING: 25,
    STOP_SPRINTING: 26,
    START_SNEAKING: 27,
    STOP_SNEAKING: 28,
    START_SWIMMING: 29,
    STOP_SWIMMING: 30,
    /** Initiating a new jump. Sent every time the client leaves the ground due to jumping, including auto jumps. */
    START_JUMPING: 31,
    START_GLIDING: 32,
    STOP_GLIDING: 33,
    PERFORM_ITEM_INTERACTION: 34,
    PERFORM_BLOCK_ACTIONS: 35,
    PERFORM_ITEM_STACK_REQUEST: 36,
    HANDLED_TELEPORT: 37,
    EMOTING: 38,
    /** Left-clicking the air. In vanilla, this generates an ATTACK_NODAMAGE sound and does nothing else. */
    MISSED_SWING: 39,
    START_CRAWLING: 40,
    STOP_CRAWLING: 41,
}