/**
 * Data Definition: Diagnostic Knowledge & Learning Assessment Question Banks
 * Realistic questions calibrated across Easy, Medium, and Hard difficulties with topic tags.
 */
const ASSESSMENT_QUESTION_BANKS = {
  // 1. School Mathematics (Standard 1-10)
  school_math: {
    domainName: 'School Mathematics',
    questions: [
      {
        id: 'sm_1',
        topic: 'Arithmetic & Number Sense',
        difficulty: 'easy',
        question: 'What is the value of 3/4 + 2/5 in simplest fraction form?',
        options: ['5/9', '23/20', '15/20', '7/20'],
        correctIndex: 1,
        explanation: 'Find the common denominator (20): (3*5 + 2*4) / 20 = (15 + 8) / 20 = 23/20.'
      },
      {
        id: 'sm_2',
        topic: 'Algebra Basics',
        difficulty: 'easy',
        question: 'If 3x + 7 = 28, what is the value of x?',
        options: ['5', '7', '9', '21'],
        correctIndex: 1,
        explanation: 'Subtract 7 from both sides: 3x = 21. Divide by 3: x = 7.'
      },
      {
        id: 'sm_3',
        topic: 'Fractions & Proportions',
        difficulty: 'medium',
        question: 'A shop gives a 20% discount on a book originally priced at ₹450. What is the final sale price?',
        options: ['₹350', '₹360', '₹380', '₹400'],
        correctIndex: 1,
        explanation: '20% of 450 = 90. Final price = 450 - 90 = ₹360.'
      },
      {
        id: 'sm_4',
        topic: 'Geometry & Mensuration',
        difficulty: 'medium',
        question: 'What is the area of a right-angled triangle with base 12 cm and hypotenuse 13 cm?',
        options: ['30 cm²', '60 cm²', '78 cm²', '25 cm²'],
        correctIndex: 0,
        explanation: 'By Pythagoras theorem, height = √(13² - 12²) = √(169 - 144) = √25 = 5 cm. Area = 1/2 * base * height = 1/2 * 12 * 5 = 30 cm².'
      },
      {
        id: 'sm_5',
        topic: 'Algebraic Identities',
        difficulty: 'hard',
        question: 'If a + b = 9 and ab = 20, what is the value of a² + b²?',
        options: ['41', '81', '61', '121'],
        correctIndex: 0,
        explanation: 'Use the identity (a + b)² = a² + b² + 2ab. Therefore, 9² = a² + b² + 2(20) => 81 = a² + b² + 40 => a² + b² = 41.'
      }
    ]
  },

  // 2. Physics & Natural Sciences (Standard 11-12 Science / Physics)
  physics_science: {
    domainName: 'Physics & Physical Sciences',
    questions: [
      {
        id: 'phy_1',
        topic: 'Units & Dimensions',
        difficulty: 'easy',
        question: 'Which of the following is the SI unit of Force?',
        options: ['Joule (J)', 'Watt (W)', 'Newton (N)', 'Pascal (Pa)'],
        correctIndex: 2,
        explanation: 'The SI unit of force is the Newton (N), defined as 1 kg·m/s².'
      },
      {
        id: 'phy_2',
        topic: 'Kinematics & Motion',
        difficulty: 'easy',
        question: 'A car starts from rest and accelerates uniformly at 2 m/s² for 5 seconds. What is its final velocity?',
        options: ['5 m/s', '10 m/s', '20 m/s', '25 m/s'],
        correctIndex: 1,
        explanation: 'Using v = u + at, where u = 0, a = 2 m/s², and t = 5 s: v = 0 + (2 * 5) = 10 m/s.'
      },
      {
        id: 'phy_3',
        topic: 'Newtonian Dynamics',
        difficulty: 'medium',
        question: 'According to Newton’s Third Law of Motion, action and reaction forces:',
        options: [
          'Act on the same body in opposite directions',
          'Act on different bodies in opposite directions',
          'Cancel each other out to produce zero acceleration',
          'Act consecutively with a slight time delay'
        ],
        correctIndex: 1,
        explanation: 'Action and reaction forces always act on two different bodies simultaneously in opposite directions.'
      },
      {
        id: 'phy_4',
        topic: 'Work, Energy & Power',
        difficulty: 'medium',
        question: 'If the velocity of a moving object is doubled, by what factor does its kinetic energy increase?',
        options: ['Remains the same', 'Doubles (2x)', 'Quadruples (4x)', 'Increases 8-fold'],
        correctIndex: 2,
        explanation: 'Kinetic Energy KE = 1/2 m v². Because KE is proportional to v², doubling velocity increases KE by 2² = 4 times.'
      },
      {
        id: 'phy_5',
        topic: 'Vectors & Rotational Motion',
        difficulty: 'hard',
        question: 'Two vectors of equal magnitude A have a resultant of magnitude A. What is the angle between the two vectors?',
        options: ['60°', '90°', '120°', '180°'],
        correctIndex: 2,
        explanation: 'R² = A² + A² + 2A² cos(θ). Here R = A, so A² = 2A²(1 + cos(θ)) => 1/2 = 1 + cos(θ) => cos(θ) = -1/2 => θ = 120°.'
      }
    ]
  },

  // 3. Commerce & Accountancy
  commerce_accountancy: {
    domainName: 'Accountancy & Financial Principles',
    questions: [
      {
        id: 'acc_1',
        topic: 'Accounting Equation',
        difficulty: 'easy',
        question: 'What is the fundamental accounting equation?',
        options: [
          'Assets = Liabilities + Owner’s Equity',
          'Assets + Liabilities = Capital',
          'Revenues - Expenses = Assets',
          'Cash Flow = Net Profit + Depreciation'
        ],
        correctIndex: 0,
        explanation: 'The fundamental balance sheet equation is: Assets = Liabilities + Owner’s Equity (Capital).'
      },
      {
        id: 'acc_2',
        topic: 'Debit & Credit Rules',
        difficulty: 'easy',
        question: 'Under the Golden Rules of Accounting, what is the rule for Real Accounts?',
        options: [
          'Debit the receiver, credit the giver',
          'Debit what comes in, credit what goes out',
          'Debit all expenses/losses, credit all incomes/gains',
          'Debit liabilities, credit assets'
        ],
        correctIndex: 1,
        explanation: 'For Real Accounts (tangible/intangible assets), the rule is: Debit what comes in, Credit what goes out.'
      },
      {
        id: 'acc_3',
        topic: 'Journal Entries & Ledger',
        difficulty: 'medium',
        question: 'When goods are purchased for cash ₹15,000, which entry is recorded in the journal?',
        options: [
          'Debit Cash Account, Credit Purchases Account',
          'Debit Purchases Account, Credit Cash Account',
          'Debit Sales Account, Credit Creditor Account',
          'Debit Inventory Account, Credit Capital Account'
        ],
        correctIndex: 1,
        explanation: 'Purchases (expense/inventory increase) is debited, and Cash (asset decrease) is credited.'
      },
      {
        id: 'acc_4',
        topic: 'Depreciation & Reserves',
        difficulty: 'medium',
        question: 'An asset is bought for ₹1,00,000 with a scrap value of ₹10,000 and 10 years useful life. Under Straight-Line Method, what is the annual depreciation?',
        options: ['₹10,000', '₹9,000', '₹8,500', '₹11,000'],
        correctIndex: 1,
        explanation: 'Annual Depreciation = (Cost - Scrap Value) / Life = (1,00,000 - 10,000) / 10 = ₹9,000 per year.'
      },
      {
        id: 'acc_5',
        topic: 'Financial Statement Analysis',
        difficulty: 'hard',
        question: 'If Current Assets are ₹2,50,000, Current Liabilities are ₹1,25,000, and Inventory is ₹50,000, what is the Quick (Acid-Test) Ratio?',
        options: ['2.0 : 1', '1.6 : 1', '1.2 : 1', '1.0 : 1'],
        correctIndex: 1,
        explanation: 'Quick Assets = Current Assets - Inventory = 2,50,000 - 50,000 = 2,00,000. Quick Ratio = 2,00,000 / 1,25,000 = 1.6 : 1.'
      }
    ]
  },

  // 4. Computer Science / JavaScript & Web Development
  javascript_web: {
    domainName: 'Modern JavaScript & Web Engineering',
    questions: [
      {
        id: 'js_1',
        topic: 'Variables & Scoping',
        difficulty: 'easy',
        question: 'Which keyword declares a block-scoped variable that cannot be reassigned?',
        options: ['var', 'let', 'const', 'static'],
        correctIndex: 2,
        explanation: '`const` defines block-scoped identifiers that cannot be reassigned after declaration.'
      },
      {
        id: 'js_2',
        topic: 'Functions & Arrow Syntax',
        difficulty: 'easy',
        question: 'What does the expression `[1, 2, 3].map(x => x * 2)` return?',
        options: ['[1, 2, 3]', '[2, 4, 6]', '6', '[2, 3, 4]'],
        correctIndex: 1,
        explanation: '`map()` creates a new array by applying the callback to each element: 1*2=2, 2*2=4, 3*2=6.'
      },
      {
        id: 'js_3',
        topic: 'Arrays & Object Mutation',
        difficulty: 'medium',
        question: 'What is the output of `typeof null` in standard JavaScript?',
        options: ['"null"', '"undefined"', '"object"', '"boolean"'],
        correctIndex: 2,
        explanation: 'Due to a historical implementation quirk in JavaScript, `typeof null` returns `"object"`.'
      },
      {
        id: 'js_4',
        topic: 'DOM & Event Loop',
        difficulty: 'medium',
        question: 'What is the order of execution in: `console.log("A"); setTimeout(() => console.log("B"), 0); Promise.resolve().then(() => console.log("C")); console.log("D");`?',
        options: ['A, B, C, D', 'A, D, B, C', 'A, D, C, B', 'A, C, D, B'],
        correctIndex: 2,
        explanation: 'Synchronous code runs first (A, D), then microtasks in Promise queue (C), then macrotasks from timer queue (B).'
      },
      {
        id: 'js_5',
        topic: 'Closures & Scope Chain',
        difficulty: 'hard',
        question: 'What does a closure allow an inner function to access?',
        options: [
          'Only its own local variables',
          'Variables from its enclosing outer lexical scope even after the outer function has closed',
          'Only global variables and window object',
          'Any private variables across unrelated functions'
        ],
        correctIndex: 1,
        explanation: 'A closure is the combination of a function bundled together with references to its surrounding lexical environment.'
      }
    ]
  },

  // 5. Python & Data Science
  python_tech: {
    domainName: 'Python Programming & Data Concepts',
    questions: [
      {
        id: 'py_1',
        topic: 'Syntax & Data Types',
        difficulty: 'easy',
        question: 'Which of the following creates an immutable sequence in Python?',
        options: ['[1, 2, 3]', '{1, 2, 3}', '(1, 2, 3)', '{"a": 1, "b": 2}'],
        correctIndex: 2,
        explanation: 'Parentheses `(1, 2, 3)` define a tuple, which is immutable in Python.'
      },
      {
        id: 'py_2',
        topic: 'Lists & Slicing',
        difficulty: 'easy',
        question: 'What is the result of `"Python"[::-1]`?',
        options: ['"Python"', '"nohtyP"', '"P"', 'SyntaxError'],
        correctIndex: 1,
        explanation: 'The slice `[::-1]` reverses the sequence, yielding `"nohtyP"`.'
      },
      {
        id: 'py_3',
        topic: 'Dictionaries & Sets',
        difficulty: 'medium',
        question: 'What will `len({1, 2, 2, 3, 3, 3})` output?',
        options: ['6', '3', '1', 'TypeError'],
        correctIndex: 1,
        explanation: 'Sets in Python only hold unique elements, so `{1, 2, 2, 3, 3, 3}` becomes `{1, 2, 3}` with length 3.'
      },
      {
        id: 'py_4',
        topic: 'Control Flow & Comprehensions',
        difficulty: 'medium',
        question: 'What is the output of `[x for x in range(6) if x % 2 == 0]`?',
        options: ['[1, 3, 5]', '[0, 2, 4]', '[2, 4, 6]', '[0, 1, 2, 3, 4, 5]'],
        correctIndex: 1,
        explanation: 'The list comprehension filters even numbers from range(6) [0 to 5], returning `[0, 2, 4]`.'
      },
      {
        id: 'py_5',
        topic: 'Functions & Lambdas',
        difficulty: 'hard',
        question: 'In Python, what is the purpose of `*args` and `**kwargs` in a function definition?',
        options: [
          'To define pointer references as in C/C++',
          'To accept an arbitrary number of positional arguments (*args) and keyword arguments (**kwargs)',
          'To force type checking on arguments',
          'To make the function execute asynchronously'
        ],
        correctIndex: 1,
        explanation: '`*args` collects extra positional arguments into a tuple, while `**kwargs` collects keyword arguments into a dictionary.'
      }
    ]
  },

  // 6. Spoken English & Language Learning
  english_language: {
    domainName: 'Spoken English & Communication Grammar',
    questions: [
      {
        id: 'eng_1',
        topic: 'Grammar & Subject-Verb Agreement',
        difficulty: 'easy',
        question: 'Choose the grammatically correct sentence:',
        options: [
          'Neither of the students were present.',
          'Neither of the students was present.',
          'Neither of the students are present.',
          'Neither of the student were present.'
        ],
        correctIndex: 1,
        explanation: '"Neither" is singular and takes the singular verb "was".'
      },
      {
        id: 'eng_2',
        topic: 'Vocabulary & Synonyms',
        difficulty: 'easy',
        question: 'Which word is the closest synonym for "Resilient"?',
        options: ['Fragile', 'Adaptable & Tough', 'Aggressive', 'Indifferent'],
        correctIndex: 1,
        explanation: '"Resilient" describes the ability to withstand adversity or bounce back quickly.'
      },
      {
        id: 'eng_3',
        topic: 'Tenses & Modals',
        difficulty: 'medium',
        question: 'Complete the sentence: "By this time next year, she ______ her Master’s degree."',
        options: [
          'will complete',
          'will have completed',
          'has completed',
          'is completing'
        ],
        correctIndex: 1,
        explanation: 'The Future Perfect tense ("will have completed") denotes an action that will finish before a specific future time.'
      },
      {
        id: 'eng_4',
        topic: 'Idiomatic Nuance',
        difficulty: 'medium',
        question: 'What does the idiom "bite the bullet" mean in professional conversation?',
        options: [
          'To act rashly without thought',
          'To face an inevitable difficult situation with fortitude',
          'To cancel a scheduled meeting',
          'To deliver unexpected bad news'
        ],
        correctIndex: 1,
        explanation: '"Bite the bullet" means accepting and enduring a hardship or painful task courageously.'
      },
      {
        id: 'eng_5',
        topic: 'Complex Sentence Structure',
        difficulty: 'hard',
        question: 'Identify the sentence containing a dangling modifier:',
        options: [
          'Walking through the park, the flowers smelled wonderful.',
          'While walking through the park, Sarah admired the flowers.',
          'The flowers smelled wonderful as we walked through the park.',
          'Sarah walked through the park and enjoyed the fragrance of flowers.'
        ],
        correctIndex: 0,
        explanation: 'In "Walking through the park, the flowers smelled wonderful", the participle phrase "Walking..." illogically modifies "the flowers".'
      }
    ]
  },

  // 7. General Analytical & Conceptual Diagnostic (Adaptive Fallback for any subject)
  general_diagnostic: {
    domainName: 'Analytical Reasoning & Conceptual Foundations',
    questions: [
      {
        id: 'gen_1',
        topic: 'Core Fundamentals',
        difficulty: 'easy',
        question: 'When approaching a new complex topic, what is the most effective foundational strategy?',
        options: [
          'Memorize final conclusions without checking principles',
          'Break down core concepts into first principles and foundational definitions',
          'Skip basic definitions and attempt only difficult edge cases',
          'Rely purely on intuition without practicing problems'
        ],
        correctIndex: 1,
        explanation: 'First-principles breakdown enables deep structural understanding and adaptable knowledge retention.'
      },
      {
        id: 'gen_2',
        topic: 'Logical Reasoning & Deduction',
        difficulty: 'easy',
        question: 'If statement A implies statement B, and statement B is false, what must be true about statement A?',
        options: [
          'Statement A must be true',
          'Statement A must be false (Modus Tollens)',
          'Statement A could be either true or false',
          'No conclusion can be drawn'
        ],
        correctIndex: 1,
        explanation: 'By the law of contraposition (Modus Tollens): If A -> B and not B, then not A.'
      },
      {
        id: 'gen_3',
        topic: 'Pattern Analysis & Data Interpretation',
        difficulty: 'medium',
        question: 'Consider the sequence: 3, 7, 15, 31, 63, ... What is the next number in this sequence?',
        options: ['95', '125', '127', '129'],
        correctIndex: 2,
        explanation: 'Each term follows the formula 2n + 1 (or 2^(k+1) - 1): 2*63 + 1 = 126 + 1 = 127.'
      },
      {
        id: 'gen_4',
        topic: 'Systemic Problem Solving',
        difficulty: 'medium',
        question: 'When diagnosing a breakdown in a multi-step workflow, what is the best initial step?',
        options: [
          'Rebuild the entire system from scratch immediately',
          'Isolate variables systematically by inspecting inputs and outputs at each milestone',
          'Change multiple parameters simultaneously to see if it fixes the issue',
          'Ignore the error if the final result appears plausible'
        ],
        correctIndex: 1,
        explanation: 'Isolating variables at each step localizes the exact failure mode without introducing confounding changes.'
      },
      {
        id: 'gen_5',
        topic: 'Critical Evaluation & Synthesis',
        difficulty: 'hard',
        question: 'What constitutes the difference between correlation and causation in analytical study?',
        options: [
          'Correlation implies identical variables, causation implies unrelated events',
          'Correlation identifies a mutual statistical association, whereas causation proves that one directly produces the other',
          'There is no meaningful difference in practical analysis',
          'Causation only applies to physical sciences, never to human systems'
        ],
        correctIndex: 1,
        explanation: 'Correlation shows co-occurrence; causation requires establishing a verified mechanism and ruling out confounding variables.'
      }
    ]
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { ASSESSMENT_QUESTION_BANKS };
}
