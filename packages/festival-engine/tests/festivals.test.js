/**
 * Test Suite: DJV Festival Engine
 * packages/festival-engine/tests/festivals.test.js
 */

const assert = require('assert');
const {
  FESTIVAL_RULES,
  MASA_NAMES,
  getFestivalById,
  findTithiDates,
  getFestivalsForYear,
} = require('../src/festivals');

console.log('--- Testing DJV Festival Engine ---');

// Test 1: MASA_NAMES has 12 months
assert.strictEqual(MASA_NAMES.length, 12, 'Must have 12 lunar months');
assert.strictEqual(MASA_NAMES[0], 'Chaitra');
assert.strictEqual(MASA_NAMES[11], 'Phalguna');
console.log('✓ 12 Lunar months verified');

// Test 2: FESTIVAL_RULES is populated
assert(Array.isArray(FESTIVAL_RULES) && FESTIVAL_RULES.length > 0, 'FESTIVAL_RULES must be an array');
console.log(`✓ FESTIVAL_RULES loaded with ${FESTIVAL_RULES.length} festival rules`);

// Test 3: getFestivalById returns valid object
const ugadi = getFestivalById('ugadi');
assert(ugadi, 'Ugadi rule must exist');
assert.strictEqual(ugadi.name, 'Ugadi');
assert.strictEqual(ugadi.nameTe, 'ఉగాది');
assert.strictEqual(ugadi.rule.paksha, 'shukla');
assert.strictEqual(ugadi.rule.tithi, 1);
assert.strictEqual(ugadi.rule.masa, 'Chaitra');
console.log('✓ Ugadi rule successfully retrieved and verified');

const shivaratri = getFestivalById('maha-shivaratri');
assert(shivaratri, 'Maha Shivaratri rule must exist');
assert.strictEqual(shivaratri.rule.paksha, 'krishna');
assert.strictEqual(shivaratri.rule.tithi, 14);
console.log('✓ Maha Shivaratri rule successfully retrieved and verified');

// Test 4: non-existent festival returns null
assert.strictEqual(getFestivalById('non-existent-festival-xyz'), null);
console.log('✓ Invalid ID returns null');

// Test 5: Verify rule properties
FESTIVAL_RULES.forEach(f => {
  assert(f.id, `Festival must have id: ${JSON.stringify(f)}`);
  assert(f.name, `Festival ${f.id} must have name`);
  assert(f.type, `Festival ${f.id} must have type`);
});
console.log('✓ All festival rules have valid structure');

console.log('All festival-engine tests passed successfully!');
